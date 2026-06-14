<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';

exigirLoginApi();

header('Content-Type: application/json; charset=utf-8');

try {
    $metodo = $_SERVER['REQUEST_METHOD'];

    if ($metodo === 'GET') {
        responder(listarClientes());
    }

    if ($metodo === 'POST') {
        $payload = lerJson();
        salvarCliente($payload);
        responder(['ok' => true]);
    }

    if ($metodo === 'DELETE') {
        $id = $_GET['id'] ?? '';
        excluirCliente((string) $id);
        responder(['ok' => true]);
    }

    http_response_code(405);
    responder(['erro' => 'Metodo nao permitido']);
} catch (Throwable $erro) {
    http_response_code(500);
    responder(['erro' => $erro->getMessage()]);
}

function responder(array $dados): void
{
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

function lerJson(): array
{
    $json = file_get_contents('php://input');
    $dados = json_decode((string) $json, true);

    if (!is_array($dados)) {
        throw new RuntimeException('JSON invalido.');
    }

    return $dados;
}

function dataOuNulo(?string $data): ?string
{
    $data = trim((string) $data);
    return $data === '' ? null : $data;
}

function listarClientes(): array
{
    $stmt = db()->query('SELECT * FROM clientes ORDER BY criado_em DESC');

    return array_map('mapearCliente', $stmt->fetchAll());
}

function mapearCliente(array $linha): array
{
    return [
        'id' => $linha['id'],
        'nome' => $linha['nome'],
        'cpf' => $linha['cpf'],
        'nascimento' => $linha['nascimento'],
        'telefone' => $linha['telefone'],
        'email' => $linha['email'],
        'endereco' => $linha['endereco'],
        'unidade' => $linha['unidade'],
        'beneficio' => $linha['beneficio'],
        'status' => $linha['status'],
        'responsavel' => $linha['responsavel'],
        'dataAtendimento' => $linha['data_atendimento'],
        'prioridade' => $linha['prioridade'],
        'proximoRetorno' => $linha['proximo_retorno'],
        'documentosChecklist' => json_decode((string) $linha['documentos_json'], true) ?: [],
        'observacoes' => $linha['observacoes'],
        'criadoEm' => $linha['criado_em'],
        'atualizadoEm' => $linha['atualizado_em'],
    ];
}

function salvarCliente(array $cliente): void
{
    $id = (string) ($cliente['id'] ?? time());
    $cpf = trim((string) ($cliente['cpf'] ?? ''));

    $duplicado = db()->prepare('SELECT id FROM clientes WHERE cpf = :cpf AND id <> :id LIMIT 1');
    $duplicado->execute([':cpf' => $cpf, ':id' => $id]);

    if ($duplicado->fetch()) {
        http_response_code(409);
        responder(['erro' => 'Ja existe um cliente cadastrado com este CPF.']);
    }

    $sql = "INSERT INTO clientes (
        id, nome, cpf, nascimento, telefone, email, endereco, unidade, beneficio, status,
        responsavel, data_atendimento, prioridade, proximo_retorno, documentos_json,
        observacoes, criado_em, atualizado_em
    ) VALUES (
        :id, :nome, :cpf, :nascimento, :telefone, :email, :endereco, :unidade, :beneficio, :status,
        :responsavel, :data_atendimento, :prioridade, :proximo_retorno, :documentos_json,
        :observacoes, :criado_em, :atualizado_em
    ) ON DUPLICATE KEY UPDATE
        nome = VALUES(nome),
        cpf = VALUES(cpf),
        nascimento = VALUES(nascimento),
        telefone = VALUES(telefone),
        email = VALUES(email),
        endereco = VALUES(endereco),
        unidade = VALUES(unidade),
        beneficio = VALUES(beneficio),
        status = VALUES(status),
        responsavel = VALUES(responsavel),
        data_atendimento = VALUES(data_atendimento),
        prioridade = VALUES(prioridade),
        proximo_retorno = VALUES(proximo_retorno),
        documentos_json = VALUES(documentos_json),
        observacoes = VALUES(observacoes),
        atualizado_em = VALUES(atualizado_em)";

    $agora = date('Y-m-d H:i:s');
    $stmt = db()->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':nome' => trim((string) ($cliente['nome'] ?? '')),
        ':cpf' => $cpf,
        ':nascimento' => dataOuNulo($cliente['nascimento'] ?? null),
        ':telefone' => trim((string) ($cliente['telefone'] ?? '')),
        ':email' => trim((string) ($cliente['email'] ?? '')) ?: null,
        ':endereco' => trim((string) ($cliente['endereco'] ?? '')) ?: null,
        ':unidade' => trim((string) ($cliente['unidade'] ?? '')),
        ':beneficio' => trim((string) ($cliente['beneficio'] ?? '')),
        ':status' => trim((string) ($cliente['status'] ?? 'Em análise')),
        ':responsavel' => trim((string) ($cliente['responsavel'] ?? '')) ?: null,
        ':data_atendimento' => dataOuNulo($cliente['dataAtendimento'] ?? null),
        ':prioridade' => trim((string) ($cliente['prioridade'] ?? 'Normal')),
        ':proximo_retorno' => dataOuNulo($cliente['proximoRetorno'] ?? null),
        ':documentos_json' => json_encode($cliente['documentosChecklist'] ?? [], JSON_UNESCAPED_UNICODE),
        ':observacoes' => trim((string) ($cliente['observacoes'] ?? '')) ?: null,
        ':criado_em' => data('criadoEm', $cliente) ?: $agora,
        ':atualizado_em' => $agora,
    ]);
}

function data(string $campo, array $dados): ?string
{
    if (empty($dados[$campo])) {
        return null;
    }

    $timestamp = strtotime((string) $dados[$campo]);
    return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
}

function excluirCliente(string $id): void
{
    if ($id === '') {
        throw new RuntimeException('ID nao informado.');
    }

    $stmt = db()->prepare('DELETE FROM clientes WHERE id = :id');
    $stmt->execute([':id' => $id]);
}
