<?php

    // Creates 5 TestModels
    use function Pest\Laravel\getJson;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    it('can include relationships ', function () {
        $test = TestModel::factory()->create();
        $test->relatedModels()->create(['name' => 'A']);
        $res = getJson('/test?includes=relatedModels');
        expect($res->getData())
            ->{0}->related_models
            ->toHaveLength(1);
    });

    it('can include nested relationships', function () {
        $test = TestModel::factory()->create();
        $nested = $test->relatedModels()->create(['name' => 'A']);
        $nested->nestedRelatedModels()->create(['name' => 'B']);
        $res = getJson('/test?includes=relatedModels.nestedRelatedModels');
        expect($res->getData())
            ->{0}->related_models
            ->{0}->nested_related_models
            ->toHaveLength(1);
    });
