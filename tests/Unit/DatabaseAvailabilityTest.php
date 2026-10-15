<?php

namespace Tests\Unit;

use App\Exceptions\DatabaseUnavailableException;
use App\Support\DatabaseAvailability;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Concerns\SimulatesMysqlFailures;

/**
 * A decisao de que tudo o resto depende: isto foi o MySQL a ficar sem
 * ligacoes, ou foi um erro nosso?
 */
class DatabaseAvailabilityTest extends TestCase
{
    use SimulatesMysqlFailures;

    /** @test */
    public function reconhece_o_erro_1226_de_ligacoes_esgotadas(): void
    {
        $this->assertTrue(DatabaseAvailability::isUnavailable($this->maxUserConnectionsException()));
    }

    /** @test */
    public function reconhece_a_ligacao_recusada(): void
    {
        $this->assertTrue(DatabaseAvailability::isUnavailable($this->connectionRefusedException()));
    }

    /** @test */
    public function reconhece_demasiadas_ligacoes_no_servidor(): void
    {
        $excecao = $this->mysqlException(1040, 'HY000', 'SQLSTATE[HY000] [1040] Too many connections');

        $this->assertTrue(DatabaseAvailability::isUnavailable($excecao));
    }

    /** @test */
    public function reconhece_a_ligacao_perdida_a_meio(): void
    {
        $excecao = $this->mysqlException(2006, 'HY000', 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away');

        $this->assertTrue(DatabaseAvailability::isUnavailable($excecao));
    }

    /** @test */
    public function reconhece_a_excecao_do_disjuntor(): void
    {
        $this->assertTrue(DatabaseAvailability::isUnavailable(DatabaseUnavailableException::circuitOpen(30)));
    }

    /** @test */
    public function segue_a_cadeia_de_excecoes(): void
    {
        $embrulhada = new RuntimeException('a renderizar a pagina', 0, $this->maxUserConnectionsException());

        $this->assertTrue(DatabaseAvailability::isUnavailable($embrulhada));
    }

    /** @test */
    public function um_erro_de_sql_nosso_nao_e_indisponibilidade(): void
    {
        $this->assertFalse(DatabaseAvailability::isUnavailable($this->syntaxErrorException()));
    }

    /** @test */
    public function uma_tabela_inexistente_nao_e_indisponibilidade(): void
    {
        $excecao = $this->mysqlException(
            1146,
            '42S02',
            "SQLSTATE[42S02]: Base table or view not found: 1146 Table 'yoyota.jobs' doesn't exist"
        );

        $this->assertFalse(DatabaseAvailability::isUnavailable($excecao));
    }

    /** @test */
    public function um_erro_vulgar_de_php_nao_e_indisponibilidade(): void
    {
        $this->assertFalse(DatabaseAvailability::isUnavailable(new RuntimeException('indice indefinido')));
    }
}
