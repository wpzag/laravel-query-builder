<?php

    namespace Wpzag\QueryBuilder\Exceptions;

    use Illuminate\Http\Response;
    use Symfony\Component\HttpKernel\Exception\HttpException;

    final class InvalidColumnException extends HttpException
    {
        public function __construct(public string $unknownFilter, public array $allowedFilters)
        {
            $allowedFilters = collect($allowedFilters)->map(function ($item) {
                return str_replace(':exact', '', $item);
            })->implode(', ');

            $message = "Requested filter `{$unknownFilter}` is not allowed. Allowed filter(s) are `{$allowedFilters}`.";

            parent::__construct(Response::HTTP_BAD_REQUEST, $message);
        }

        public static function columnNotAllowed(string $column, array $allowedFilters): static
        {
            return new static(...func_get_args());
        }
    }
