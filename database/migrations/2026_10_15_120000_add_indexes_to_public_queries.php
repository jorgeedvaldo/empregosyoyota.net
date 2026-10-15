<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Indices para as consultas que as paginas publicas fazem a toda a hora.
 *
 * Isto e a outra metade do problema das 30 ligacoes: uma consulta que
 * demora meio segundo segura a ligacao durante meio segundo. Com as tabelas
 * a crescer, um "where country_id = 1 order by id desc" sem indice passa a
 * ler a tabela toda, e as ligacoes acumulam-se a espera umas das outras.
 *
 * A migracao e defensiva de proposito: o esquema em producao foi crescendo
 * a mao (ha colunas que nao estao em migracao nenhuma), por isso cada
 * indice so e criado se a coluna existir e se nao houver ja um indice a
 * comecar por ela.
 */
return new class extends Migration
{
    /**
     * tabela => lista de colunas a indexar.
     */
    protected $indexes = [
        'jobs' => ['country_id', 'slug'],
        'articles' => ['country_id', 'slug'],
        'curriculos' => ['slug'],
    ];

    public function up()
    {
        foreach ($this->indexes as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column) || $this->isIndexed($table, $column)) {
                    continue;
                }

                Schema::table($table, function ($blueprint) use ($column) {
                    $blueprint->index($column);
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->indexes as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                $name = $table . '_' . $column . '_index';

                if (! $this->hasIndexNamed($table, $name)) {
                    continue;
                }

                Schema::table($table, function ($blueprint) use ($name) {
                    $blueprint->dropIndex($name);
                });
            }
        }
    }

    /**
     * Ja existe algum indice que comece por esta coluna? Um indice composto
     * (country_id, id) serve igualmente para filtrar por country_id, por
     * isso nao se cria outro por cima.
     */
    protected function isIndexed(string $table, string $column): bool
    {
        foreach ($this->indexList($table) as $index) {
            if (($index['seq'] ?? null) == 1 && ($index['column'] ?? null) === $column) {
                return true;
            }
        }

        return false;
    }

    protected function hasIndexNamed(string $table, string $name): bool
    {
        foreach ($this->indexList($table) as $index) {
            if (($index['name'] ?? null) === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, array{name: string, column: string, seq: int}>
     */
    protected function indexList(string $table): array
    {
        $connection = Schema::getConnection();

        if ($connection->getDriverName() !== 'mysql') {
            return [];
        }

        // wrapTable() ja aplica o prefixo configurado da ligacao.
        $rows = DB::select('SHOW INDEX FROM ' . $connection->getQueryGrammar()->wrapTable($table));

        return array_map(function ($row) {
            $row = (array) $row;

            return [
                'name' => $row['Key_name'] ?? '',
                'column' => $row['Column_name'] ?? '',
                'seq' => (int) ($row['Seq_in_index'] ?? 0),
            ];
        }, $rows);
    }
};
