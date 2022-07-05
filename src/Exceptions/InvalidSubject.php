<?php

    namespace Wpzag\QueryBuilder\Exceptions;

    use InvalidArgumentException;

    final class InvalidSubject extends InvalidArgumentException
    {
        public static function make($subject): static
        {
            return new static(
                sprintf(
                    'Subject %s is invalid.',
                    is_object($subject)
                        ? sprintf('class `%s`', get_class($subject))
                        : sprintf('type `%s`', gettype($subject))
                )
            );
        }
    }
