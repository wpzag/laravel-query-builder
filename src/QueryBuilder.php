<?php

    namespace Wpzag\QueryBuilder;

    use Closure;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\Relation;
    use Illuminate\Pipeline\Pipeline;
    use Wpzag\QueryBuilder\Exceptions\InvalidSubject;
    use Wpzag\QueryBuilder\Pipelines\AppendsPipeline;
    use Wpzag\QueryBuilder\Pipelines\FilterPipeline;
    use Wpzag\QueryBuilder\Pipelines\IncludesPipeline;
    use Wpzag\QueryBuilder\Pipelines\PaginationPipeline;
    use Wpzag\QueryBuilder\Pipelines\SortPipeline;

    class QueryBuilder
    {
        public function __construct(
            public                       $subject,
            public ?Closure              $callback = null,
            public ?array                $pipelines = null,
            public ?Builder              $query = null,
            public ?LengthAwarePaginator $paginator = null
        ) {
        }

        private function buildPipeline(): self
        {
            $query = $this->getQuery();
            $pipelines = $this->getPipelines();

            $this->query = app(Pipeline::class)
                ->send($query)
                ->through($pipelines)
                ->thenReturn();

            return $this;
        }

        private function getQuery(): Builder
        {
            if (is_subclass_of($this->subject, Model::class)) {
                return $this->subject::query();
            }
            if ($this->subject instanceof Builder) {
                return $this->subject;
            }
            if ($this->subject instanceof Relation) {
                return $this->subject->getQuery();
            }
            if ($this->subject instanceof Model) {
                return $this->subject->newQuery();
            }

            throw InvalidSubject::make($this->subject);
        }

        public static function for($subject, ?Closure $callback = null, array $pipelines = null): self
        {
            return (new self(subject: $subject, callback: $callback, pipelines: $pipelines))
                ->buildPipeline();
        }

        private function getPipelines(): array
        {
            return $this->pipelines ??= array_filter([
                IncludesPipeline::class,
                SortPipeline::class,
                FilterPipeline::class,
                $this->callback ?: null,
            ]);
        }

        public function query(): Builder
        {
            return $this->query;
        }

        public function get(): Collection
        {
            return $this->query->get();
        }

        public function withPagination(): LengthAwarePaginator
        {
            $this->paginator = app(Pipeline::class)
                ->send($this->query)
                ->through(PaginationPipeline::class)
                ->thenReturn();

            return $this->paginator;
        }

        public function withPaginationAndAppends(): LengthAwarePaginator
        {
            $this->withPagination();

            return app(Pipeline::class)
                ->send($this->paginator)
                ->through(AppendsPipeline::class)
                ->thenReturn();
        }

        public function withAppends(): Collection
        {
            return app(Pipeline::class)
                ->send($this->query())
                ->through(AppendsPipeline::class)
                ->thenReturn();
        }
    }
