<?php

namespace App\Support;

use App\Exceptions\DatabaseUnavailableException;
use Illuminate\Database\DetectsLostConnections;
use Throwable;

/**
 * Distingue "a base de dados nao esta disponivel agora" de "esta consulta
 * tem um erro".
 *
 * A diferenca e toda: a primeira recupera sozinha e justifica servir a
 * ultima copia valida do conteudo; a segunda e um erro de programacao, que
 * deve continuar a dar 500 e a aparecer no log como tal.
 */
class DatabaseAvailability
{
    use DetectsLostConnections;

    /**
     * Codigos de erro do MySQL que significam indisponibilidade temporaria.
     *
     * O 1226 e o que motivou tudo isto: o alojamento partilhado limita a 30
     * as ligacoes simultaneas deste utilizador.
     */
    const FAILURE_CODES = [
        1040, // Too many connections
        1129, // Host is blocked because of many connection errors
        1130, // Host is not allowed to connect to this MySQL server
        1203, // User already has more than 'max_user_connections' active connections
        1226, // User has exceeded the 'max_user_connections' resource
        2002, // Can't connect to local MySQL server
        2003, // Can't connect to MySQL server
        2006, // MySQL server has gone away
        2013, // Lost connection to MySQL server during query
    ];

    /**
     * Pedacos de mensagem que denunciam uma falha de ligacao e que o
     * DetectsLostConnections do Laravel nao cobre (os limites de ligacoes
     * nao sao ligacoes perdidas: sao ligacoes que nunca chegaram a existir).
     */
    const FAILURE_MESSAGES = [
        'max_user_connections',
        'max_connections',
        'too many connections',
        'has exceeded the',
        'can\'t connect to',
        'connection refused',
        'connection timed out',
        'no connection could be made',
    ];

    public static function isUnavailable(Throwable $e): bool
    {
        return (new static)->matches($e);
    }

    protected function matches(Throwable $e): bool
    {
        for ($current = $e; $current !== null; $current = $current->getPrevious()) {
            if ($current instanceof DatabaseUnavailableException) {
                return true;
            }

            if ($this->causedByLostConnection($current)) {
                return true;
            }

            if ($this->hasFailureCode($current) || $this->hasFailureMessage($current)) {
                return true;
            }
        }

        return false;
    }

    /**
     * O numero do erro do MySQL vive no errorInfo[1] da PDOException (que a
     * QueryException copia para si). O getCode() traz o SQLSTATE, que para o
     * 1226 e apenas "42000" — partilhado com erros de sintaxe, logo inutil
     * para esta decisao.
     */
    protected function hasFailureCode(Throwable $e): bool
    {
        $errorInfo = isset($e->errorInfo) ? $e->errorInfo : null;

        if (is_array($errorInfo) && isset($errorInfo[1]) && in_array((int) $errorInfo[1], self::FAILURE_CODES, true)) {
            return true;
        }

        // Falha a abrir a ligacao: nao ha errorInfo, o numero vem no code.
        return in_array((int) $e->getCode(), [2002, 2003, 2006, 2013], true);
    }

    protected function hasFailureMessage(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        foreach (self::FAILURE_MESSAGES as $needle) {
            if (str_contains($message, $needle)) {
                return true;
            }
        }

        return false;
    }
}
