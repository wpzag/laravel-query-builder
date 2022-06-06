<?php

    namespace Wpzag\QueryBuilder\Services;

    use Illuminate\Support\Facades\Schema;

    final class ConfigParser
    {
        public static function parse(): void
        {
            (new ConfigParser())->parseConfig();
        }

        private function parseConfig(): void
        {
            if (is_array(config('query-builder.models'))) {
                foreach (config('query-builder.models') as $model => $options) {
                    $this->generateModelOptions($model, $options);
                }
            }
        }

        private function generateModelOptions(string $model, array $options): void
        {
            foreach ($options as $key => $value) {
                if (! in_array($key, ['sortable', 'filterable'])) {
                    continue;
                }
                $this->generateOptionValues($model, $key, $value);
            }
        }

        private function generateOptionValues(string $model, string $key, array $values): void
        {
            $ValueWithInitialAsterisk = $this->getValueWithInitialAsterisk($values);
            if (! $ValueWithInitialAsterisk) {
                return;
            }

            $hasExceptions = $this->initialAsteriskValueHasExceptions($ValueWithInitialAsterisk);
            $exceptions = $hasExceptions ? $this->getExceptionsArray($ValueWithInitialAsterisk) : [];

            $configColumns = $this->removeWildCardValues($values);

            $modelColumns = $this->getModelColumns($model);
            $columns = [...$modelColumns, ...$configColumns];
            $filteredColumns = $this->removeExceptionsFromColumns($columns, $exceptions);

            config(['query-builder.models.' . $model . '.' . $key => $filteredColumns]);
        }

        private function getValueWithInitialAsterisk(array $columns): ?string
        {
            return collect($columns)->first(fn ($item) => str($item)->startsWith('*'));
        }

        private function initialAsteriskValueHasExceptions(string $ValueWithInitialAsterisk): bool
        {
            return str($ValueWithInitialAsterisk)->contains(':except:');
        }

        private function getExceptionsArray(string $ValueWithInitialAsterisk): array
        {
            return explode(',', str($ValueWithInitialAsterisk)->after(':except:'));
        }

        private function removeWildCardValues(array $values): array
        {
            return collect($values)->reject(fn ($i) => str($i)->contains('*'))->toArray();
        }

        private function getModelColumns(string $model): array
        {
            $model = new $model();
            $table = $model->getTable();

            return Schema::getColumnListing($table);
        }

        private function removeExceptionsFromColumns(array $columns, array $exceptions): array
        {
            return collect($columns)->filter(fn ($i) => ! in_array($i, $exceptions))->values()->toArray();
        }
    }
