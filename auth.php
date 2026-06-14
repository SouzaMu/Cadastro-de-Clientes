<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function usuarioLogado(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin(): void
{
    if (usuarioLogado() !== null) {
        return;
    }

    header('Location: login.php');
    exit;
}

function exigirLoginApi(): void
{
    if (usuarioLogado() !== null) {
        return;
    }

    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['erro' => 'Login necessario.'], JSON_UNESCAPED_UNICODE);
    exit;
}

function tentarLogin(string $usuario, string $senha): bool
{
    $stmt = db()->prepare('SELECT id, nome, usuario, senha_hash FROM usuarios WHERE usuario = :usuario LIMIT 1');
    $stmt->execute([':usuario' => $usuario]);
    $dados = $stmt->fetch();

    if (!$dados || !password_verify($senha, $dados['senha_hash'])) {
        return false;
    }

    $_SESSION['usuario'] = [
        'id' => (int) $dados['id'],
        'nome' => $dados['nome'],
        'usuario' => $dados['usuario'],
    ];

    session_regenerate_id(true);

    return true;
}
