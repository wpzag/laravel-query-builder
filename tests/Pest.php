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
