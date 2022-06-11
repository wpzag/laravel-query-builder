<?php

    namespace Spatie\QueryBuilder\Exceptions;

    use Illuminate\Http\Response;
    use Illuminate\Support\Collection;
    use Symfony\Component\HttpKernel\Exception\HttpException;

    class InvalidFilterQuery extends HttpException
    {
        public function __construct(public Collection $unknownFilters, public Collection $allowedFilters)
        {
            $unknownFilters = $this->unknownFilters->implode(', ');
            $allowedFilters = $this->allowedFilters->implode(', ');
            $message = "Requested filter(s) `{$unknownFilters}` are not allowed. Allowed filter(s) are `{$allowedFilters}`.";

            parent::__construct(Response::HTTP_BAD_REQUEST, $message);
        }

        public static function filtersNotAllowed(Collection $unknownFilters, Collection $allowedFilters): static
        {
            return new static(...func_get_args());
        }
    }
