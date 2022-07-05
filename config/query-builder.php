<?php
	
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
//			TestModel::class => [
//				'includes' => ['posts.comments.author', 'posts.user.comments.author', 'comments.author'],
//				'appends' => ['appendedValue'],
//				'filterable' => ['*:except:excepted_field', 'posts.title', 'posts.body', 'posts.created_at'],
//				'sortable' => ['*'],
//				'max_per_page' => 10,
//			],
		],
		'methods' => [
			// two arguments, value must be an array of two values
			'range' => [
				'between' => 'whereBetween',
				'not-between' => 'whereNotBetween',
			],
			// two argument, value must be an array of values
			'arrays' => [
				'in' => 'whereIn',
				'not-in' => 'whereNotIn',
			],
			// three arguments, value must be a string
			'general' => [
				'time' => 'whereTime',
				'day' => 'whereDay',
				'month' => 'whereMonth',
				'year' => 'whereYear',
				'date' => 'whereDate',
			],
			// one argument , the column name
			'one_arg' => [
				'empty' => 'whereNull',
				'not-empty' => 'whereNotNull'
			],
		
		],
		'operators' => [
			'gt' => '>',
			'gte' => '>=',
			'lt' => '<',
			'lte' => '<=',
			'ne' => '!=',
		],
		'disable_invalid_filter_query_exception' => true,
		'disable_invalid_sort_exception' => true,
		'disable_invalid_include_query_exception' => true,
		'disable_invalid_appends_exception' => true,
	
	];
