<?php

    namespace Wpzag\QueryBuilder\Tests;

    use Illuminate\Database\Eloquent\Factories\Factory;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Orchestra\Testbench\TestCase as Orchestra;
    use Wpzag\QueryBuilder\QueryBuilderServiceProvider;

    class TestCase extends Orchestra
    {
        use DatabaseMigrations;

        protected function setUp(): void
        {
            parent::setUp();
            $this->setUpDatabase($this->app);
            Factory::guessFactoryNamesUsing(
                fn (string $modelName) => 'Wpzag\\QueryBuilder\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
            );
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
                $table->string('name');
                $table->integer('age');
                $table->string('address');
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
    }
