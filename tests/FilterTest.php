<?php

    // Creates 5 TestModels
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Wpzag\QueryBuilder\QueryBuilder;
    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    beforeEach(fn () => createModels());

    it('wont filter if the filter param is missing')->getJson('/test')->assertJsonCount(5);
    it('wont filter if the column name is missing')->getJson('/test?filter')->assertJsonCount(5);
    it('wont filter if the column has no values')->getJson('/test?filter[name]=')->assertJsonCount(5);
    it('wont filter if the column is wrong')->getJson('/test?filter[random]=one')->assertJsonCount(5);
    it('wont filter if the column is not allowed in the config')->getJson('/test?filter[excepted_field]=one')->assertJsonCount(5);

    it('can filter by a single column and single value')
        ->getJson('/test?filter[name]=one')
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'one']);

    it('can filter by a single column and multiple values')
        ->getJson('/test?filter[name]=one,two,three')
        ->assertJsonCount(3)
        ->assertJsonFragment(['name' => 'one'])
        ->assertJsonFragment(['name' => 'two'])
        ->assertJsonFragment(['name' => 'three']);

    it('can filter by multiple columns and single values')
        ->getJson('/test?filter[common]=common&filter[age]=10')
        ->assertJsonCount(1)
        ->assertJsonFragment(['age' => 10]);

    it('can filter by multiple columns and multiple values')
        ->getJson('/test?filter[common]=common&filter[age]=10,20')
        ->assertJsonCount(2)
        ->assertJsonFragment(['age' => 10])
        ->assertJsonFragment(['age' => 20]);

    it('can filter with an greater than operator')
        ->getJson('/test?filter[age]=gt.30')
        ->assertJsonCount(2)
        ->assertJsonFragment(['age' => 40])
        ->assertJsonFragment(['age' => 50]);


    it('can filter with an greater than or equal operator')
        ->getJson('/test?filter[age]=gte.40')
        ->assertJsonCount(2)
        ->assertJsonFragment(['age' => 40])
        ->assertJsonFragment(['age' => 50]);

    it('can filter with an less than operator')
        ->getJson('/test?filter[age]=lt.30')
        ->assertJsonCount(2)
        ->assertJsonFragment(['age' => 10])
        ->assertJsonFragment(['age' => 20]);

    it('can filter with an less than or equal operator')
        ->getJson('/test?filter[age]=lte.20')
        ->assertJsonCount(2)
        ->assertJsonFragment(['age' => 10])
        ->assertJsonFragment(['age' => 20]);

    it('can filter with an not equal operator')
        ->getJson('/test?filter[age]=ne.10')
        ->assertJsonCount(4)
        ->assertJsonFragment(['age' => 20])
        ->assertJsonFragment(['age' => 30])
        ->assertJsonFragment(['age' => 40])
        ->assertJsonFragment(['age' => 50]);

    it('can filter with an operator and multiple columns')
        ->getJson('/test?filter[name]=one&filter[age]=lt.30')
        ->assertJsonCount(1)
        ->assertJsonFragment(['name' => 'one']);

    it('can filter with an operator and multiple values')
        ->getJson('/test?filter[age]=lt.30,50')
        ->assertJsonCount(3)
        ->assertJsonFragment(['age' => 10])
        ->assertJsonFragment(['age' => 20])
        ->assertJsonFragment(['age' => 50]);

    it('can filter between two values')
        ->getJson('/test?filter[age][between]=[35,50]')
        ->assertSuccessful()
        ->assertJsonCount(2)
        ->assertJsonFragment(['age' => 40])
        ->assertJsonFragment(['age' => 50]);

    it('can filter not between two values')
        ->getJson('/test?filter[age][not-between]=[20,50]')
        ->assertJsonCount(1)
        ->assertJsonFragment(['age' => 10]);
    it('can filter in a list')
        ->getJson('/test?filter[age][in]=[10,20,30]')
        ->assertJsonCount(3)
        ->assertJsonFragment(['age' => 10])
        ->assertJsonFragment(['age' => 20])
        ->assertJsonFragment(['age' => 30]);

    it('can filter not in a list')
        ->getJson('/test?filter[age][not-in]=[10,20,30]')
        ->assertJsonCount(2)
        ->assertJsonFragment(['age' => 40])
        ->assertJsonFragment(['age' => 50]);

    it('can filter by date')
        ->getJson('/test?filter[created_at][date]=2020-01-01')
        ->assertJsonCount(1)
        ->assertJsonFragment(['created_at' => Carbon::parse('2020-01-01 01:00:00')->toISOString()]);

    it('can filter by  time')
        ->getJson('/test?filter[created_at][time]=02:00:00')
        ->assertJsonCount(1)
        ->assertJsonFragment(['created_at' => Carbon::parse('2020-02-02 02:00:00')->toISOString()]);

    it('can filter by day')
        ->getJson('/test?filter[created_at][day]=2')
        ->assertJsonCount(1)
        ->assertJsonFragment(['created_at' => Carbon::parse('2020-02-02 02:00:00')->toISOString()]);

    it('can filter by month')
        ->getJson('/test?filter[created_at][month]=2')
        ->assertJsonCount(1)
        ->assertJsonFragment(['created_at' => Carbon::parse('2020-02-02 02:00:00')->toISOString()]);

    it('can filter by year')
        ->getJson('/test?filter[created_at][year]=2020')
        ->assertJsonCount(5);

    it('can filter by a null columns', function () {
        TestModel::find(1)->update(['age' => null]);
        $this->getJson('/test?filter[age][empty]')->assertJsonCount(1);
    });

    it('can filter by a none-null columns', function () {
        TestModel::query()->update(['age' => null]);
        TestModel::find(1)->update(['age' => 10]);
        $this->getJson('/test?filter[age][empty]')->assertJsonCount(4);
    });

    it('wont ignore falsy values', function () {
        $this->getJson('/test?filter[age]=0')
            ->assertJsonCount(0);
    });

    it('can filter by related model', function () {
        TestModel::find(1)->relatedModels()->create(['name' => 'one']);
        $this->getJson('/test?filter[relatedModels.name]=one')
            ->assertJsonCount(1);
    });
    it('can filter by nested related model', function () {
        $related = TestModel::find(1)->relatedModels()->create(['name' => 'one']);
        QueryBuilder::for(subject: TestModel::class, request: new Request(['filter' => ['relatedModels.relatedModels.name' => 'one']]))->dd();
        $related->nestedRelatedModels()->create(['name' => 'nested']);
        $this->getJson('/test?filter[relatedModels.nestedRelatedModels.name]=dfs')
            ->assertJsonCount(1);
    });
