<?php

    use Wpzag\QueryBuilder\Services\ConfigParser;

    it('can parse filterable columns', function (array $config, array $expected) {
        setConfig('filterable', $config);
        ConfigParser::parse();
        expect(getConfig('filterable'))->toMatchArray($expected);
    })->with([
        [['name', 'relatedModels.name'], ['name', 'relatedModels.name'],],
        [['*'], ['id', 'name', 'age', 'address', 'is_visible', 'created_at', 'updated_at']],
        [['*:except:id,created_at,updated_at'], ['name', 'age', 'address', 'is_visible']],
        [['*:except:id,created_at,updated_at', 'relatedModels.name'], ['name', 'age', 'address', 'is_visible', 'relatedModels.name']],
    ]);
