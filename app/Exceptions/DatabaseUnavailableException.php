<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

/**
 * A base de dados nao esta a responder e nao existe copia do conteudo
 * pedido. Quem apanha isto e o Handler, que responde com 503 e a pagina de
 * indisponibilidade — nunca com um sucesso inventado.
 */
class DatabaseUnavailableException extends RuntimeException
{
    /** @var int Segundos sugeridos ao visitante (e aos motores de busca). */
    protected $retryAfter;

    public function __construct(string $message, int $retryAfter = 120, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->retryAfter = max(1, $retryAfter);
    }

    /**
     * Envolve o erro original do MySQL, preservando-o como "previous" para
     * o log manter o rasto tecnico completo.
     */
    public static function from(Throwable $e, int $retryAfter = 120): self
    {
        return new self('A base de dados nao respondeu: ' . $e->getMessage(), $retryAfter, $e);
    }

    /**
     * O disjuntor esta aberto: nem se chegou a tentar a ligacao.
     */
    public static function circuitOpen(int $retryAfter): self
    {
        return new self(
            'Ligacao a base de dados suspensa pelo disjuntor apos falhas consecutivas.',
            $retryAfter
        );
    }

    public function retryAfter(): int
    {
        return $this->retryAfter;
    }
}
