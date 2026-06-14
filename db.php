<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'escritorio_advocacia';
const DB_USER = 'root';
const DB_PASS = '';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $serverDsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $server = new PDO($serverDsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $server->exec(
        'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` ' .
        'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
    );

    $pdo = new PDO($serverDsn . ';dbname=' . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    criarTabelas($pdo);

    return $pdo;
}

function criarTabelas(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS clientes (
            id VARCHAR(40) PRIMARY KEY,
            nome VARCHAR(180) NOT NULL,
            cpf VARCHAR(20) NOT NULL UNIQUE,
            nascimento DATE NULL,
            telefone VARCHAR(30) NOT NULL,
            email VARCHAR(180) NULL,
            endereco VARCHAR(255) NULL,
            unidade VARCHAR(80) NOT NULL,
            beneficio VARCHAR(120) NOT NULL,
            status VARCHAR(60) NOT NULL,
            responsavel VARCHAR(120) NULL,
            data_atendimento DATE NULL,
            prioridade VARCHAR(30) NOT NULL DEFAULT 'Normal',
            proximo_retorno DATE NULL,
            documentos_json LONGTEXT NULL,
            observacoes TEXT NULL,
            criado_em DATETIME NOT NULL,
            atualizado_em DATETIME NOT NULL,
            INDEX idx_status (status),
            INDEX idx_proximo_retorno (proximo_retorno),
            INDEX idx_prioridade (prioridade)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}
