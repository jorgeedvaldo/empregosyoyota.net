-- =====================================================================
--  Empregos Yoyota — Tabela `settings`
--  Cria a tabela de definicoes (usada pelo toggle de anuncios AdSense),
--  semeia o valor inicial e regista a migration como ja executada,
--  para que `php artisan migrate` a salte sem conflitos.
--
--  Uso:
--    mysql -u UTILIZADOR -p NOME_DA_BASE_DADOS < create_settings_table.sql
--  (ou cole o conteudo no phpMyAdmin / Adminer)
--
--  Idempotente: pode ser executado mais do que uma vez sem erros nem
--  perda de dados existentes.
-- =====================================================================

-- 1) Tabela `settings` (equivalente a migration do Laravel)
CREATE TABLE IF NOT EXISTS `settings` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`        VARCHAR(191) NOT NULL,
    `value`      TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Valor inicial: anuncios LIGADOS ('1').
--    Se a chave ja existir, mantem o valor atual (nao sobrescreve).
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`)
VALUES ('ads_enabled', '1', NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = `updated_at`;

-- 3) Regista a migration como executada, para o `php artisan migrate`
--    nao a voltar a correr. Os SELECT sobre `migrations` estao envoltos
--    em subconsultas (derived tables) para evitar o erro 1093 do MySQL.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_04_000000_create_settings_table',
       (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM (SELECT `batch` FROM `migrations`) AS b)
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM (SELECT `migration` FROM `migrations`) AS m
    WHERE m.`migration` = '2026_07_04_000000_create_settings_table'
);
