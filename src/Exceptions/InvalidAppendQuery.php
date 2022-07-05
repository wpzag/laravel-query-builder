<?php

    namespace Wpzag\QueryBuilder\Exceptions;

    use Symfony\Component\HttpFoundation\Response as ResponseAlias;
    use Symfony\Component\HttpKernel\Exception\HttpException;

    final class InvalidAppendQuery extends HttpException
    {
        public function __construct(public string $unknownInclude, public array $allowedAppends)
        {
            $allowedAppendsString = collect($allowedAppends)->implode(', ');

            $message = "Requested appends `{$unknownInclude}` is not allowed. Allowed include(s) are `{$allowedAppendsString}`.";


            parent::__construct(ResponseAlias::HTTP_BAD_REQUEST, $message);
        }

        public static function columnNotAllowed(string $column, array $allowedAppends): static
        {
            return new static(...func_get_args());
        }
    }
