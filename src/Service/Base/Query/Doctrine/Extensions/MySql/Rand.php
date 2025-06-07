<?php

namespace Smug\Core\Service\Base\Query\Doctrine\Extensions\MySql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

class Rand extends FunctionNode
{
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker): string
    {
        return 'RAND()';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}