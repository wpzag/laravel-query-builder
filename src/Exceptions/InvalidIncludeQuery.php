<?php

    namespace Wpzag\QueryBuilder\Exceptions;

    use Symfony\Component\HttpFoundation\Response as ResponseAlias;
    use Symfony\Component\HttpKernel\Exception\HttpException;

    class InvalidIncludeQuery extends HttpException
    {
        public function __construct(public string $unknownInclude, public array $allowedIncludes)
        {
            $allowedIncludesString = collect($allowedIncludes)->implode(', ');

            $message = "Requested includes `{$unknownInclude}` is not allowed. Allowed include(s) are `{$allowedIncludesString}`.";


            parent::__construct(ResponseAlias::HTTP_BAD_REQUEST, $message);
        }

        public static function columnNotAllowed(string $column, array $allowedIncludes): static
        {
            return new static(...func_get_args());
        }
    }
