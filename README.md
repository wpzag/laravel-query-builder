# Build up Eloquent queries from the Query string parameters of the reuqest .

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wpzag/laravel-query-builder.svg?style=flat-square)](https://packagist.org/packages/wpzag/laravel-query-builder)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/wpzag/laravel-query-builder/run-tests?label=tests)](https://github.com/wpzag/laravel-query-builder/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/wpzag/laravel-query-builder/Check%20&%20fix%20styling?label=code%20style)](https://github.com/wpzag/laravel-query-builder/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/wpzag/laravel-query-builder.svg?style=flat-square)](https://packagist.org/packages/wpzag/laravel-query-builder)

This package allows you to filter, sort, append, paginate and load relations based on the request query parameters.

## Installation

You can install the package via composer:

```bash
composer require wpzag/laravel-query-builder
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="query-builder-config"
```

## Usage

### Add config for your model in query-builder.php

```php
'models' => [
    User::class => [
       'includes' => ['posts.comments.author', ],
       'appends' => ['fullname','avatar'],
       'filterable' => ['*:except:role', 'posts.title', 'posts.body', 'posts.created_at'],
       'sortable' => ['*'],
       'max_per_page' => 20             
     ],   
 ]       
```

#### [ * ] means all the model's attribute except hidden .

#### [  *:except:column1,column2   ] You can also exclude some attributes by adding :except: then comma separated attributes .

#### [  age:exact   ] If you prefixed the attribute with :extact it will use the ( = )  operator instead of ( LIKE ) operator in the query.

# Usage

```php 

$builder = QueryBuilder::for(User:class); //This applies includes/filters/sorts to the query

$builder->query() // Returns the query 

$builder->get() // Returns the eloquent collection

$builder->withPagination() // Applies pagination to the query

$builder->withAppends() // Applies appends to the query

$builder->withPaginationAndAppends() // Applies pagination & appends to the query

```

### You can also add custom query to query-builder

```php 

QueryBuilder::for(User:class)->query()->where('id', '>', 1)->get();


// Or if you are using  withPagination(),withAppends() or withPaginationAndAppends() :
QueryBuilder::for(User:class, function($query){

	//extra queries here
	
	return $next($query);
})->withPaginationAndAppends();
```

# Examples:

### Filtering

```php
users?filter[name]=john,rose

// User::where('name','like','%john%')
//       ->orWhere('name','like','%rose%')->get();
```

```php
users?filter[name,username]=john

// User::where('name','like','%john%')
//       ->orWhere('username','like','%rose%')->get();
```

```php
users?filter[name]=john,rose&filter[age]=gt.20

// User::where('name','like','%john%')
//       ->orWhere('name','like','%rose%')
//       ->where('age','>',20)->get();
```

```php
users?filter[age][between]=[29,40]

// User::whereBetween('age', [29, 40])->get();
```

```php
users?filter[email_verified_at][empty]

// User::whereNull('email_verified_at')->get();
```

```php
users?filter[created_at][date]=2020-01-01

// User::whereDate('created_at', '2020-01-01')->get();
```

```php
users?filter[posts.created_at]=lt.2020-01-01

// User::whereHas('posts', function($query) {
//     $query->whereDate('created_at', '<', '2020-01-01');
// })->get();
```

```php
users?filter[role][not-in]=[admin,teacher,student]

// User::whereNotIn('role', ['admin', 'teacher', 'student'])->get();

```

### Sorting

```php
users?sort[name]=-created_at

// User::orderBy('created_at', 'desc')->get();

```

```php
users?sort[name]=-statues,name

// User::orderBy('statues', 'desc')->orderBy('name', 'asc')->get();

```

### Appending

```php
users?append=fullname

// User::get()->append('fullname')
```

### Loading relations

```php
users?include=profile,posts.comments.author

// User::with('profile','posts.comments.author')->get();

```

### Pagination

```php
users?page=2&limit=10

// User::paginate(10);

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [osama mohamed](https://github.com/wpzag)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
