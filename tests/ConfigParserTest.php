<?php

    use Wpzag\QueryBuilder\Services\ConfigParser;

    it('can parse filterable columns', function (array $config, array $expected) {
        setConfig('filterable', $config);
        ConfigParser::parse();
        expect(getConfig('filterable'))->toEqualCanonicalizing($expected);
    })->with([
        [['name', 'relatedModels.name'], ['name', 'relatedModels.name'],],
        [['name', 'age:exact'], ['name', 'age:exact'],],
        [['*'], ['id', 'name', 'age', 'common', 'is_visible', 'created_at', 'updated_at']],
        [['*:except:id,excepted,created_at,updated_at'], ['name', 'age', 'common', 'is_visible']],
        [['*:except:id,created_at,updated_at', 'relatedModels.name'], ['name', 'age', 'common', 'is_visible', 'relatedModels.name']],
    ]);
