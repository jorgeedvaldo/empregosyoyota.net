<?php

namespace Tests\Concerns;

use Illuminate\Database\QueryException;
use PDOException;

/**
 * Fabrica das excepcoes que o MySQL atira quando nao da para mais.
 *
 * Os testes simulam a falha por aqui: nunca se vai esgotar de verdade as 30
 * ligacoes do alojamento para ver o que acontece.
 */
trait SimulatesMysqlFailures
{
    /**
     * O erro que motivou tudo isto.
     */
    protected function maxUserConnectionsException(): QueryException
    {
        return $this->mysqlException(
            1226,
            '42000',
            "SQLSTATE[42000] [1226] User 'yqqnyhrgnt_edv' has exceeded the 'max_user_connections' resource (current value: 30)"
        );
    }

    /**
     * Servidor fora de alcance (nem chega a haver ligacao).
     */
    protected function connectionRefusedException(): QueryException
    {
        return $this->mysqlException(2002, 'HY000', 'SQLSTATE[HY000] [2002] Connection refused');
    }

    /**
     * Erro de SQL do programa: nao e indisponibilidade e tem de continuar a
     * dar 500.
     */
    protected function syntaxErrorException(): QueryException
    {
        return $this->mysqlException(
            1064,
            '42000',
            "SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax"
        );
    }

    protected function mysqlException(int $errno, string $sqlState, string $message): QueryException
    {
        $previous = new PDOException($message, 0);
        $previous->errorInfo = [$sqlState, $errno, $message];

        return new QueryException('select * from `jobs`', [], $previous);
    }
}
