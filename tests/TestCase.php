<?php

    namespace Wpzag\QueryBuilder\Tests;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use Orchestra\Testbench\TestCase as Orchestra;
    use Wpzag\QueryBuilder\QueryBuilder;
    use Wpzag\QueryBuilder\QueryBuilderServiceProvider;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    class TestCase extends Orchestra
    {
        use DatabaseMigrations;

        protected function setUp(): void
        {
            parent::setUp();
            $this->setUpConfig();
            $this->setUpDatabase($this->app);
            Factory::guessFactoryNamesUsing(
                fn (string $modelName) => 'Wpzag\\QueryBuilder\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
            );
            $this->setUpRoutes();
        }

        protected function getPackageProviders($app): array
        {
            return [
                QueryBuilderServiceProvider::class,
            ];
        }

        protected function setUpDatabase(Application $app)
        {
            $getSchemaBuilder = $app[ 'db' ]->connection()->getSchemaBuilder();
            $getSchemaBuilder->create('test_models', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->integer('age')->nullable();
                $table->string('common')->default('common');
                $table->boolean('is_visible')->default(true);
                $table->timestamps();
            });

            $getSchemaBuilder->create('append_models', function (Blueprint $table) {
                $table->increments('id');
                $table->string('firstname');
                $table->string('lastname');
            });

            $getSchemaBuilder->create('soft_delete_models', function (Blueprint $table) {
                $table->increments('id');
                $table->softDeletes();
                $table->string('name');
            });

            $getSchemaBuilder->create('scope_models', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
            });

            $getSchemaBuilder->create('related_models', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('test_model_id');
                $table->string('name');
            });

            $getSchemaBuilder->create('nested_related_models', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('related_model_id');
                $table->string('name');
            });

            $getSchemaBuilder->create('pivot_models', function (Blueprint $table) {
                $table->increments('id');
                $table->string('test_model_id');
                $table->integer('related_through_pivot_model_id');
                $table->string('location')->nullable();
            });

            $getSchemaBuilder->create('related_through_pivot_models', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
            });

            $getSchemaBuilder->create('morph_models', function (Blueprint $table) {
                $table->increments('id');
                $table->morphs('parent');
                $table->string('name');
            });
        }

        private function setUpRoutes()
        {
            Route::get('/test', function (Request $request) {
                $builder = QueryBuilder::for(TestModel::class);
                if ($request->debug) {
                    $builder->query()->dd();
                }
                if ($request->append && $request->page) {
                    $response = $builder->withPaginationAndAppends();
                } elseif ($request->page) {
                    $response = $builder->withPagination();
                } elseif ($request->append) {
                    $response = $builder->withAppends();
                } else {
                    $response = $builder->get();
                }

                return response()->json($response);
            });
        }

        private function setUpConfig()
        {
            config(['query-builder.models' => [
                TestModel::class => [
                    'includes' => ['relatedModels', "relatedModels.nestedRelatedModels"],
                    'appends' => ['appended_field'],
                    'filterable' => ['name', 'age:exact', 'common', 'null_field', 'created_at', 'relatedModels.name', 'relatedModels.nestedRelatedModels.name'],
                    'sortable' => ['name', 'age'],
                    'max_per_page' => 10,
                ],],
            ]);
            config(['query-builder.disable_invalid_filter_query_exception' => false]);
            config(['query-builder.disable_invalid_sort_exception' => false]);
            config(['query-builder.disable_invalid_include_query_exception' => false]);
            config(['query-builder.disable_invalid_appends_exception' => false]);
        }
    }
