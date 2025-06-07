<?php

namespace Smug\Core\Entity\Connection\Query;

class UnsupportedPreparedStatementParameterTypeException extends \InvalidArgumentException
{
    public static function new(string $parameterType): UnsupportedPreparedStatementParameterTypeException
    {
        return new self(
            sprintf(
                "Parameter type '%s' is not allowed for prepared statement retrieved from QueryBuilder. Use executeQuery() or executeStatement() directly.",
                $parameterType
            ),
            1639245170
        );
    }
}
