# Sistema de Administração de Assistentes

Um painel administrativo moderno e responsivo para gerenciar assistentes de IA, suas conversas e mensagens. Desenvolvido em PHP com arquitetura orientada a objetos e design profissional.

## 🚀 Características

- **Interface Moderna**: Design responsivo com CSS customizado e animações suaves
- **Arquitetura Limpa**: Código PHP orientado a objetos com padrão MVC
- **CRUD Completo**: Gerenciamento completo de assistentes, conversas e mensagens
- **Dashboard Intuitivo**: Estatísticas em tempo real e visualizações
- **Sistema de Roteamento**: URLs amigáveis e navegação intuitiva
- **Segurança**: Proteção CSRF, sanitização de dados e validações
- **Responsivo**: Funciona perfeitamente em desktop e mobile

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## 🛠️ Instalação

### 1. Clone o Projeto

```bash
git clone <url-do-repositorio>
cd assistant-admin
```

### 2. Configurar Banco de Dados

Crie um banco de dados MySQL e execute o script SQL:

```sql
-- Criar banco de dados
CREATE DATABASE assistant_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco
USE assistant_admin;

-- Executar o script de criação das tabelas (database.sql)
```

### 3. Configurar Conexão

Edite o arquivo `config/database.php` com suas credenciais:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'assistant_admin');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
?>
```

### 4. Configurar Servidor Web

#### Apache

Certifique-se de que o mod_rewrite está habilitado e configure o VirtualHost:

```apache
<VirtualHost *:80>
    ServerName assistant-admin.local
    DocumentRoot /caminho/para/assistant-admin
    
    <Directory "/caminho/para/assistant-admin">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name assistant-admin.local;
    root /caminho/para/assistant-admin;
    index index.php;

    location / {
        try_files $uri $uri/ /painel/index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. Configurar Permissões

```bash
# Dar permissões de escrita para logs (se necessário)
chmod 755 -R assistant-admin/
chown -R www-data:www-data assistant-admin/
```

### 6. Acessar o Sistema

Acesse `http://assistant-admin.local/painel` no seu navegador.

## 📁 Estrutura do Projeto

```
assistant-admin/
├── config/
│   ├── config.php          # Configurações gerais
│   └── database.php        # Configurações do banco
├── src/
│   ├── Database.php        # Classe de conexão
│   ├── Controller.php      # Controller base
│   ├── Assistant.php       # Modelo Assistant
│   ├── Conversation.php    # Modelo Conversation
│   ├── Message.php         # Modelo Message
│   ├── AssistantController.php
│   └── ConversationController.php
├── painel/
│   ├── index.php          # Roteador principal
│   ├── layout.php         # Layout base
│   ├── .htaccess         # Configurações Apache
│   └── views/            # Templates das páginas
│       ├── dashboard/
│       ├── assistants/
│       ├── conversations/
│       ├── messages/
│       └── stats/
├── assets/
│   ├── css/
│   │   └── admin.css     # Estilos principais
│   └── js/
│       └── admin.js      # JavaScript
├── database.sql          # Script de criação do banco
└── README.md
```

## 🎯 Funcionalidades

### Dashboard
- Visão geral do sistema
- Estatísticas em tempo real
- Gráficos de status das conversas
- Assistentes mais ativos

### Gerenciamento de Assistentes
- ✅ Listar todos os assistentes
- ✅ Criar novo assistente
- ✅ Editar assistente existente
- ✅ Visualizar detalhes
- ✅ Excluir assistente
- ✅ Busca e filtros

### Gerenciamento de Conversas
- ✅ Listar todas as conversas
- ✅ Visualizar conversa completa
- ✅ Filtrar por status e assistente
- ✅ Atualizar status da conversa
- ✅ Exportar conversa (JSON/TXT)
- ✅ Estatísticas detalhadas

### Gerenciamento de Mensagens
- ✅ Listar todas as mensagens
- ✅ Visualizar mensagem completa
- ✅ Filtrar por conversa
- ✅ Excluir mensagens

### Recursos Adicionais
- 🔒 Proteção CSRF
- 📱 Design responsivo
- 🎨 Interface moderna
- ⚡ Carregamento rápido
- 🔍 Sistema de busca
- 📊 Relatórios e estatísticas

## 🔧 Configurações Avançadas

### Personalização do Layout

Edite `assets/css/admin.css` para personalizar cores e estilos:

```css
:root {
    --primary-color: #3b82f6;    /* Cor principal */
    --secondary-color: #64748b;  /* Cor secundária */
    --success-color: #10b981;    /* Cor de sucesso */
    --danger-color: #ef4444;     /* Cor de erro */
    --warning-color: #f59e0b;    /* Cor de aviso */
}
```

### Configurações de Timezone

Edite `config/config.php`:

```php
define('TIMEZONE', 'America/Sao_Paulo');
date_default_timezone_set(TIMEZONE);
```

### Configurações de Segurança

- Tokens CSRF são gerados automaticamente
- Sanitização de dados em todas as entradas
- Validação de tipos e formatos
- Headers de segurança configurados

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
1. Verifique as credenciais em `config/database.php`
2. Certifique-se de que o MySQL está rodando
3. Verifique se o banco de dados existe

### Erro 404 nas Páginas
1. Verifique se o mod_rewrite está habilitado (Apache)
2. Confirme se o arquivo `.htaccess` existe em `/painel/`
3. Verifique as configurações do servidor web

### Problemas de Permissão
```bash
# Corrigir permissões
sudo chown -R www-data:www-data /caminho/para/assistant-admin
sudo chmod -R 755 /caminho/para/assistant-admin
```

### Erro de Classe Não Encontrada
1. Verifique se todos os arquivos estão no lugar correto
2. Confirme se o autoload está funcionando
3. Verifique os includes nos arquivos

## 📝 Logs e Debug

Para habilitar logs de debug, edite `config/config.php`:

```php
define('DEBUG_MODE', true);
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## 🔄 Atualizações

Para atualizar o sistema:

1. Faça backup do banco de dados
2. Faça backup dos arquivos de configuração
3. Substitua os arquivos do sistema
4. Execute scripts de migração (se houver)
5. Teste todas as funcionalidades

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 👥 Suporte

Para suporte técnico:
- Abra uma issue no GitHub
- Consulte a documentação
- Verifique os logs de erro

## 🚀 Próximas Funcionalidades

- [ ] Sistema de autenticação
- [ ] Logs de auditoria
- [ ] Notificações em tempo real
- [ ] API REST
- [ ] Temas personalizáveis
- [ ] Backup automático
- [ ] Relatórios em PDF
- [ ] Integração com webhooks

---

Desenvolvido com ❤️ para facilitar o gerenciamento de assistentes de IA.

