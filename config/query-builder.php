<?php

    use Wpzag\QueryBuilder\Tests\TestClasses\Models\TestModel;

    return [
        /*
         |--------------------------------------------------------------------------
         | Default Values to be applied to all queries
         |--------------------------------------------------------------------------
         |
         | Per_page - the number of results to show per page .
         |
         */
        'defaults' => [
            'per_page' => [
                'default' => 20,
                'max' => 100,
            ],
        ],
        'models' => [
            TestModel::class => [
                'includes' => ['posts.comments.author', 'posts.user.comments.author', 'comments.author'],
                'appends' => ['appendedValue'],
                'filterable' => ['*', 'posts.title', 'posts.body', 'posts.created_at'],
                'sortable' => ['*'],
                'max_per_page' => 10,
            ],
        ],
    ];
