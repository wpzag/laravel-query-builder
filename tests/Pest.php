<?php

    use Wpzag\QueryBuilder\Tests\TestCase;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    uses(TestCase::class)->in(__DIR__);
    function setConfig(string $type, array $columns, ?string $model = TestModel::class): void
    {
        config(['query-builder.models' => [
            $model => [
                $type => [...$columns],
            ],
        ]]);
    }

    function getConfig(string $type, ?string $model = TestModel::class): array
    {
        return config("query-builder.models.$model.$type");
    }

    expect()->extend('toBeWithinRange', function ($min, $max) {
        return $this->toBeGreaterThanOrEqual($min)
            ->toBeLessThanOrEqual($max);
    });


    function createModels(): void
    {
        TestModel::factory(5)
            ->sequence(
                fn ($sequence) => ['name' => 'one', 'age' => 10, 'created_at' => '2020-01-01 01:00:00'],
                ['name' => 'two', 'age' => 20, 'created_at' => '2020-02-02 02:00:00'],
                ['name' => 'three', 'age' => 30, 'created_at' => '2020-03-03 03:00:00'],
                ['name' => 'four', 'age' => 40, 'created_at' => '2020-04-04 04:00:00'],
                ['name' => 'five', 'age' => 50, 'created_at' => '2020-05-05 05:00:00']
            )
            ->create();
    }
