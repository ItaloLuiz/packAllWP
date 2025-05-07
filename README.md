# Instruções para Configuração do packAllWP com GitHub

Este documento fornece instruções para configurar o plugin packAllWP para receber atualizações via GitHub, permitindo distribuir o plugin para seus clientes sem compartilhar suas credenciais do GitHub.

## Configuração Inicial

1. Crie um repositório no GitHub chamado `packAllWP` (ou o nome que preferir)
2. No arquivo `packAllWP/includes/github-updater.php`, edite as seguintes linhas:
   ```php
   $this->github_username = 'seu-usuario-github'; // Altere para seu usuário GitHub
   $this->github_repo = 'packAllWP'; // Altere para o nome do seu repositório
   ```

3. Se você alterou o nome do repositório, certifique-se de atualizar também a URL no cabeçalho do arquivo principal do plugin (`packAllWP.php`):
   ```php
   /**
    * Plugin URI: https://github.com/seu-usuario/packAllWP
    */
   ```

## Fluxo de Trabalho para Atualizações

Para publicar novas versões do plugin:

1. Edite o arquivo `packAllWP.php` e atualize a versão:
   ```php
   define('PACK_ALL_WP_VERSION', '1.0.1'); // Incremente o número da versão
   ```

2. Faça commit das alterações e envie para o GitHub

3. No repositório GitHub, vá para a seção "Releases" e crie uma nova release:
   - Tag version: deve corresponder à versão no arquivo PHP (ex: 1.0.1)
   - Release title: nome da versão (ex: "Versão 1.0.1")
   - Descrição: liste as mudanças e melhorias feitas nesta versão
   - Faça upload do arquivo ZIP do plugin ou deixe o GitHub criar o ZIP automaticamente

4. Publique a release

## Distribuição do Plugin

Você pode distribuir o plugin para seus clientes de duas maneiras:

### 1. Instalação Manual (Recomendado)

1. Baixe o ZIP mais recente da seção "Releases" do seu repositório GitHub
2. Envie para seus clientes e instrua-os a instalar pelo painel do WordPress (Plugins > Adicionar Novo > Enviar Plugin)

### 2. Link Direto

Forneça aos clientes o link direto para o ZIP da versão mais recente:
```
https://github.com/seu-usuario/packAllWP/archive/refs/tags/v1.0.0.zip
```

## Verificação de Atualizações

Seus clientes verão uma notificação de atualização no painel de administração do WordPress quando você publicar uma nova versão no GitHub, sem precisar compartilhar suas credenciais de acesso.

## Estrutura de Arquivos do Plugin

```
packAllWP/
├── packAllWP.php                 # Arquivo principal do plugin
├── index.php                     # Arquivo de segurança
├── includes/                     # Funcionalidades principais
│   ├── github-updater.php        # Sistema de atualização via GitHub
│   └── index.php                 # Arquivo de segurança
├── modules/                      # Diretório de módulos
│   ├── index.php                 # Arquivo de segurança
│   ├── quiz-hub/                 # Módulo Quiz Hub
│   │   ├── quiz-hub.php          # Arquivo principal do módulo
│   │   ├── index.php             # Arquivo de segurança
│   │   ├── templates/            # Templates do módulo
│   │   │   └── quiz-hub-template.php
│   │   └── assets/               # Recursos do módulo
│   │       └── css/
│   │           └── quiz-hub.css
│   └── button/                   # Módulo Button
│       ├── button.php            # Arquivo principal do módulo
│       ├── index.php             # Arquivo de segurança
│       └── assets/               # Recursos do módulo
│           └── css/
│               └── button.css
└── README.md                     # Documentação do plugin
```

## Adicionando Novos Módulos

Para adicionar um novo módulo ao plugin:

1. Crie uma nova pasta no diretório `modules/` com o nome do seu módulo
2. Adicione um arquivo PHP principal com o mesmo nome do módulo
3. Atualize o método `load_modules()` no arquivo `packAllWP.php` para incluir seu novo módulo

Exemplo:
```php
// Carrega o módulo MeuNovoModulo
if (file_exists(PACK_ALL_WP_DIR . 'modules/meu-novo-modulo/meu-novo-modulo.php')) {
    require_once PACK_ALL_WP_DIR . 'modules/meu-novo-modulo/meu-novo-modulo.php';
}
```