<?php

namespace Wpzag\QueryBuilder\Commands;

use Illuminate\Console\Command;

class QueryBuilderCommand extends Command
{
    public $signature = 'laravel-query-builder';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
