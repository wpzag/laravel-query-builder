<?php

    namespace Wpzag\QueryBuilder\Filters;

    use Illuminate\Support\Arr;
    use Wpzag\QueryBuilder\Exceptions\InvalidColumnException;

    trait FilterHelpers
    {
        private function getMultipleArraysFromStringValue(string $value): array
        {
            $arr = explode('],[', $value);

            return array_map(function ($item) {
                $str = str($item)->remove('],');
                $str = $str->remove('[')->remove(']')->value();

                return explode(',', $str);
            }, $arr);
        }

        private function makeFiltersArray(string|array|null $filter): ?array
        {
            $filtersArray = [];
            if (! $this->isValidFilter($filter)) {
                return null;
            }
            $method = is_array($filter) ? array_key_first($filter) : '';
            $values = is_array($filter) ? $filter[ $method ] : $filter;

            if ($this->filterHasMethod($filter)) {
                $values = $this->getValues($method, $values);
                if (! $this->isOneArgMethod($method)) {
                    foreach ($values as $value) {
                        $filtersArray[] = $this->makeValueArray($value, $method);
                    }
                } else {
                    $filtersArray[] = $this->makeValueArray($values, $method);
                }
            } else {
                foreach ($this->getArrayFromStringValue($values) as $value) {
                    $filtersArray[] = $this->makeValueArray($value, $method);
                }
            }


            return $filtersArray;
        }

        private function getOperator($value): string
        {
            $namedOperator = str($value)->before('.')->value();

            return match ($namedOperator) {
                'gt' => '>',
                'gte' => '>=',
                'lt' => '<',
                'lte' => '<=',
                'ne' => '!=',

                default => 'like'
            };
        }

        private function getValue(string $value): ?string
        {
            return str($value)->after('.')->value();
        }

        private function getMethodName(string $method): string
        {
            $array = collect($this->methods)->first(fn ($array) => key_exists($method, $array));

            return $array[ $method ] ?? 'where';
        }

        private function makeValueArray(string|array|null $value, string $method): array
        {
            return [
                'params_count' => $this->getParamsCount($method),
                'value' => ! $this->doesntRequireOperator($method) ? $this->getValue($value) : $value,
                'method' => $this->getMethodName($method),
                'operator' => ! $this->doesntRequireOperator($method) ? $this->getOperator($value) : null,
            ];
        }

        private function isValidFilter(array|string|int|null $filter): bool
        {
            // Filter must not be empty
            if ($filter === null || $filter === '') {
                return false;
            }

            // If a string filter return true
            if (is_string($filter) || is_int($filter)) {
                return true;
            }

            // If an array, check its first key assigned to an array
            if (is_array(Arr::first($filter))) {
                return false;
            }

            $method = array_key_first($filter);

            // Is a valid method
            if (! $this->isValidFilerMethod($method)) {
                return false;
            }

            $value = $filter[ $method ];

            if ($this->isOneArgFilter($value, $method)) {
                return true;
            }


            if ((empty($value) || ! is_string($value))) {
                return false;
            }

            $valuesArrays = $this->getMultipleArraysFromStringValue($value);
            $condition = false;

            foreach ($valuesArrays as $value) {
                if (empty($value)) {
                    return false;
                }

                // Check if it's a range filter, make sure the value has brackets
                if ($condition = $this->isValidRangeFilter($value, $method)) {
                    continue;
                }
                if ($condition = $this->isValidArrayFilter($value, $method)) {
                    continue;
                }

                // if none of the above, return false
                $condition = $this->isGeneralMethod($method);
            }

            return $condition;
        }

        private function stringHasArrayShape(string $value): bool
        {
            return str($value)->startsWith('[') && str($value)->endsWith(']');
        }

        private function isRangeMethod(string $method): bool
        {
            return key_exists($method, $this->methods[ 'range' ]);
        }

        private function isArrayMethod(string $method): bool
        {
            return key_exists($method, $this->methods[ 'arrays' ]);
        }

        private function isOneArgMethod(string $method): bool
        {
            return key_exists($method, $this->methods[ 'one_arg' ]);
        }

        private function isGeneralMethod(string $method): bool
        {
            return key_exists($method, $this->methods[ 'general' ]);
        }

        private function isValidFilerMethod(mixed $method): bool
        {
            if (empty($method) || ! is_string($method)) {
                return false;
            }

            $method = collect($this->methods)->first(fn ($methodsArray) => key_exists($method, $methodsArray));

            return ! empty($method);
        }

        private function isValidRangeFilter(array $array, string $method): bool
        {
            return
                $this->isRangeMethod($method) &&
                count($array) === 2;
        }

        private function isValidArrayFilter(array $value, string $method): bool
        {
            return $this->isArrayMethod($method) && count($value) > 0;
        }

        private function isOneArgFilter(?string $value, string $method): bool
        {
            return $this->isOneArgMethod($method) && is_null($value);
        }

        /**
         * @param array|string|null $filter
         * @return bool
         */
        private function filterHasMethod(array|string|null $filter): bool
        {
            return is_array($filter);
        }

        private function getParamsCount($method): int
        {
            return [
                    $this->isOneArgMethod($method) => 1,
                    $this->isArrayMethod($method) || $this->isRangeMethod($method) => 2,
                    $this->isGeneralMethod($method) => 3,
                ][ true ] ?? 3;
        }

        private function filterRequiresArrayValue(string $method): bool
        {
            return $this->isArrayMethod($method) || $this->isRangeMethod($method);
        }

        private function doesntRequireOperator(string $method): bool
        {
            return $this->isRangeMethod($method) || $this->isArrayMethod($method) || $this->isOneArgMethod($method);
        }

        private function getArrayFromStringValue(string $value): array
        {
            $str = str($value)->replace('[', '')->replace(']', '');

            return explode(',', $str);
        }

        private function getValues(string $method, ?string $values): ?array
        {
            if ($this->isOneArgMethod($method)) {
                return null;
            }
            if ($this->filterRequiresArrayValue($method)) {
                return $this->getMultipleArraysFromStringValue($values);
            }

            return $this->getArrayFromStringValue($values);
        }

        private function columnIsAllowed(string $column): bool
        {
            $allowed = fn ($column) => in_array($column, $this->allowedFilters);

            if (! $allowed($column) && ! $allowed($column . ':exact') && ! config('query-builder.disable_invalid_filter_query_exception')) {
                throw  InvalidColumnException::columnNotAllowed($column, $this->allowedFilters);
            }

            return $allowed($column) || $allowed($column . ':exact');
        }

        private function isColumnExact(string $column): bool
        {
            return collect($this->allowedFilters)->contains($column . ':exact');
        }
    }
