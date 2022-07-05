<?php

    // Creates 5 TestModels
    use Carbon\Carbon;
    use function Pest\Laravel\getJson;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    it('can sort string columns ascendingly ', function () {
        TestModel::factory(2)->sequence(fn ($sequence) => ['name' => 'A'], ['name' => 'B'])->create();
        $res = getJson('/test?sort=name');
        expect($res->getData())
            ->toHaveLength(2)
            ->{0}->name
            ->toBe('A');
    });
    it('can sort string columns descendingly ', function () {
        TestModel::factory(2)->sequence(fn ($sequence) => ['name' => 'A'], ['name' => 'B'])->create();
        $res = getJson('/test?sort=-name');
        expect($res->getData())
            ->toHaveLength(2)
            ->{0}->name
            ->toBe('B');
    });
    it('can sort numeric columns', function () {
        TestModel::factory(2)->sequence(fn ($sequence) => ['age' => 10], ['age' => 20])->create();
        $res = getJson('/test?sort=-age');
        expect($res->getData())
            ->toHaveLength(2)
            ->{0}->age
            ->toBe(20);
    });
    it('can sort datetime columns', function () {
        TestModel::factory(2)->sequence(fn ($sequence) => ['created_at' => Carbon::yesterday()], ['created_at' => Carbon::tomorrow()])->create();
        $res = getJson('/test?sort=created_at');
        expect($res->getData())
            ->toHaveLength(2)
            ->{0}->created_at
            ->toBe(Carbon::yesterday()->toISOString());
    });
