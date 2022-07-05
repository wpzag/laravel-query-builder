<?php

    namespace Wpzag\QueryBuilder\Pipelines;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Support\Arr;
    use Wpzag\QueryBuilder\Exceptions\InvalidIncludeQuery;

    class IncludesPipeline extends BasePipeline
    {
        protected Builder $query;
        private ?array $includes;
        private ?string $includeParam;

        public function handle(Builder $query, Closure $next): Builder
        {
            $this->query = $query;
            $this->includes = $this->getOptions(query: $this->query, option: 'includes');
            $this->includeParam = request()->includes;


            if (empty($this->includeParam)) {
                return $next($this->query);
            }

            $this->isSingleRelationship() ?
                $this->loadSingleRelationship()
                : $this->loadMultipleRelationship();


            return $next($this->query);
        }

        public function isSingleRelationship(): bool
        {
            return ! str($this->includeParam)->contains(',');
        }

        public function loadSingleRelationship(): void
        {
            if ($this->checkIfRelationshipExists($this->includeParam)) {
                $this->query = $this->query->with($this->includeParam);
            }
        }

        private function checkIfRelationshipExists(string $relationship): string|null
        {
            $first = Arr::first(
                $this->includes,
                fn ($el) => $relationship === $el ||
                    ! str($relationship)->contains('.') &&
                    $relationship === str($el)->before('.')->value()
            );
            if (is_null($first) && ! config('query-builder.disable_invalid_include_query_exception')) {
                throw InvalidIncludeQuery::columnNotAllowed(column: $relationship, allowedIncludes: $this->includes);
            }

            return $first;
        }

        private function loadMultipleRelationship(): void
        {
            $relationships = explode(',', $this->includeParam);
            $validRelationships = [];
            foreach ($relationships as $relationship) {
                if ($this->checkIfRelationshipExists($relationship)) {
                    $validRelationships[] = $relationship;
                }
            }
            $this->query = $this->query->with($validRelationships);
        }
    }
