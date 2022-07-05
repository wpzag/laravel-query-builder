<?php

    namespace Wpzag\QueryBuilder;

    use Closure;
    use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\Relation;
    use Illuminate\Http\Request;
    use Illuminate\Pipeline\Pipeline;
    use Wpzag\QueryBuilder\Exceptions\InvalidSubject;
    use Wpzag\QueryBuilder\Pipelines\FilterPipeline;
    use Wpzag\QueryBuilder\Pipelines\SortPipeline;

    class QueryBuilder
    {
        public function __construct(
            public          $subject,
            public ?Closure $callback = null,
            public ?Request $request = null,
            public ?array   $pipelines = null
        ) {
        }

        private function buildPipeline(): mixed
        {
            $query = $this->getQuery();
            $request = $this->getRequest();
            $pipelines = $this->getPipelines();

            return app(Pipeline::class)
                ->send(['query' => $query, 'request' => $request])
                ->through($pipelines)
                ->thenReturn();
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

        private function getRequest(): Request
        {
            return $this->request ?? request();
        }

        public static function for($subject, ?Closure $callback = null, ?Request $request = null, array $pipelines = null): mixed
        {
            return (new self(subject: $subject, callback: $callback, request: $request, pipelines: $pipelines))
                ->buildPipeline();
        }

        private function getPipelines(): array
        {
            return $this->pipelines ??= array_filter([
                FilterPipeline::class,
                SortPipeline::class,
                $this->callback,
            ]);
        }
    }
