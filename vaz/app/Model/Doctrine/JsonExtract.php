<?php

namespace App\Model\Doctrine;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

class JsonExtract extends FunctionNode
{
    public $jsonField;
    public $jsonPath;

    public function parse(Parser $parser)
    {
        $parser->StringExpression();
        $parser->Match('(');
        $this->json = $parser->ArithmeticPrimary();
        $parser->Match(',');
        $this->path = $parser->ArithmeticPrimary();
        $parser->Match(')');
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf(
            'JSON_EXTRACT(%s, %s)',
            $this->jsonField->dispatch($sqlWalker),
            $this->jsonPath->dispatch($sqlWalker)
        );
    }
}
