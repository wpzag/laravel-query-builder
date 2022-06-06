<?php

    namespace Wpzag\QueryBuilder;

    use Spatie\LaravelPackageTools\Package;
    use Spatie\LaravelPackageTools\PackageServiceProvider;
    use Wpzag\QueryBuilder\Services\ConfigParser;

    class QueryBuilderServiceProvider extends PackageServiceProvider
    {
        public function configurePackage(Package $package): void
        {
            $package
                ->name('laravel-query-builder')
                ->hasConfigFile();
        }

        public function boot()
        {
            ConfigParser::parse();
        }
    }
