<?php

    namespace Wpzag\QueryBuilder\Exceptions;

    use Symfony\Component\HttpFoundation\Response as ResponseAlias;
    use Symfony\Component\HttpKernel\Exception\HttpException;

    class InvalidSortQuery extends HttpException
    {
        public function __construct(public string $unknownSort, public array $allowedSorts)
        {
            $allowedSortsString = collect($allowedSorts)->implode(', ');

            $message = "Requested sorts `{$unknownSort}` is not allowed. Allowed sort(s) are `{$allowedSortsString}`.";


            parent::__construct(ResponseAlias::HTTP_BAD_REQUEST, $message);
        }

        public static function columnNotAllowed(string $column, array $allowedSorts): static
        {
            return new static(...func_get_args());
        }
    }
