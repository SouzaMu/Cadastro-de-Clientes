# Gerenciador do Escritório

## Como abrir

1. Inicie o Apache e o MySQL no XAMPP.
2. Acesse: `http://localhost/Cadastrar%20clientes/`
3. Faça login com o usuário inicial.

## Login inicial

- Usuário: `admin`
- Senha: `admin123`

Troque essa senha antes de usar com dados reais do escritório.

## Banco de dados

O sistema cria automaticamente o banco `escritorio_advocacia` e as tabelas `clientes` e `usuarios` na primeira execução.

Configuração padrão em `db.php`:

- Host: `127.0.0.1`
- Usuário: `root`
- Senha: vazia
- Banco: `escritorio_advocacia`

## Arquivos principais

- `index.html`: redirecionamento para `index.php`.
- `index.php`: tela principal protegida por login.
- `login.php`: tela de login.
- `logout.php`: saída do sistema.
- `auth.php`: controle de sessão e autenticação.
- `clientes_api.php`: API para listar, salvar e excluir clientes.
- `db.php`: conexão com MySQL e criação automática da tabela.

## Observação

Se existirem clientes antigos no `localStorage`, o sistema tenta migrar para o MySQL quando a lista do banco ainda estiver vazia.
