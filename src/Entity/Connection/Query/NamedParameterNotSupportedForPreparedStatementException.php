<?php

namespace Smug\Core\Entity\Connection\Query;

class NamedParameterNotSupportedForPreparedStatementException extends \InvalidArgumentException
{
    public static function new(string $placeholderName): NamedParameterNotSupportedForPreparedStatementException
    {
        return new self(
            sprintf(
                "Cannot prepare statement for QueryBuilder because unsupported named placeholder '%s'",
                $placeholderName
            ),
            1639249867
        );
    }
}
