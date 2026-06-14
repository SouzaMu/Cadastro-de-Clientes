<?php
declare(strict_types=1);

require __DIR__ . '/auth.php';

if (usuarioLogado() !== null) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim((string) ($_POST['usuario'] ?? ''));
    $senha = (string) ($_POST['senha'] ?? '');

    if (tentarLogin($usuario, $senha)) {
        header('Location: index.php');
        exit;
    }

    $erro = 'Usuário ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Aposentadoria Prev</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <link rel="stylesheet" href="style.css" />
</head>
<body class="login-page">
  <main class="login-shell">
    <section class="login-panel">
      <div class="login-brand">
        <div class="login-icon"><i class="fa-solid fa-shield-heart"></i></div>
        <div>
          <h1>Aposentadoria Prev</h1>
          <p>Gerenciador do escritório</p>
        </div>
      </div>

      <?php if ($erro !== ''): ?>
        <div class="alert alert-danger py-2"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <form method="post" autocomplete="off">
        <div class="mb-3">
          <label for="usuario" class="form-label">Usuário</label>
          <input type="text" class="form-control" id="usuario" name="usuario" required autofocus />
        </div>
        <div class="mb-3">
          <label for="senha" class="form-label">Senha</label>
          <input type="password" class="form-control" id="senha" name="senha" required />
        </div>
        <button type="submit" class="btn btn-prev w-100">
          <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar
        </button>
      </form>

      <div class="small-muted mt-3">
        Primeiro acesso: usuário <strong>admin</strong> e senha <strong>admin123</strong>.
      </div>
    </section>
  </main>
</body>
</html>
