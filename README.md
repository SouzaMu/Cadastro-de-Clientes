# Gerenciador do Escritório

## Como abrir

1. Inicie o Apache e o MySQL no XAMPP.
2. Acesse: `http://localhost/Cadastrar%20clientes/`

## Banco de dados

O sistema cria automaticamente o banco `escritorio_advocacia` e a tabela `clientes` na primeira execução.

Configuração padrão em `db.php`:

- Host: `127.0.0.1`
- Usuário: `root`
- Senha: vazia
- Banco: `escritorio_advocacia`

## Arquivos principais

- `index.html`: tela do sistema.
- `clientes_api.php`: API para listar, salvar e excluir clientes.
- `db.php`: conexão com MySQL e criação automática da tabela.

## Observação

Se existirem clientes antigos no `localStorage`, o sistema tenta migrar para o MySQL quando a lista do banco ainda estiver vazia.
