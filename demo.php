<?php
/**
 * Arquivo de Demonstração e Teste
 * Sistema de Administração de Assistentes
 */

// Incluir configurações
require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html>\n";
echo "<html lang='pt-BR'>\n";
echo "<head>\n";
echo "    <meta charset='UTF-8'>\n";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "    <title>Demo - Sistema de Assistentes</title>\n";
echo "    <style>\n";
echo "        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }\n";
echo "        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
echo "        .success { color: #10b981; background: #ecfdf5; padding: 10px; border-radius: 4px; margin: 10px 0; }\n";
echo "        .error { color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 4px; margin: 10px 0; }\n";
echo "        .info { color: #3b82f6; background: #eff6ff; padding: 10px; border-radius: 4px; margin: 10px 0; }\n";
echo "        .btn { display: inline-block; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; margin: 5px; }\n";
echo "        .btn:hover { background: #2563eb; }\n";
echo "        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }\n";
echo "        h1 { color: #1f2937; }\n";
echo "        h2 { color: #374151; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px; }\n";
echo "    </style>\n";
echo "</head>\n";
echo "<body>\n";
echo "<div class='container'>\n";

echo "<h1>🚀 Demo - Sistema de Administração de Assistentes</h1>\n";

// Teste de conexão com banco
echo "<h2>1. Teste de Conexão com Banco de Dados</h2>\n";
try {
    $db = new Database();
    echo "<div class='success'>✅ Conexão com banco de dados estabelecida com sucesso!</div>\n";
    
    // Testar se as tabelas existem
    $tables = ['assistants', 'conversations', 'messages'];
    foreach ($tables as $table) {
        try {
            $result = $db->fetchOne("SELECT COUNT(*) as count FROM {$table}");
            echo "<div class='info'>📊 Tabela '{$table}': {$result['count']} registros</div>\n";
        } catch (Exception $e) {
            echo "<div class='error'>❌ Erro na tabela '{$table}': {$e->getMessage()}</div>\n";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro de conexão: {$e->getMessage()}</div>\n";
    echo "<div class='info'>💡 Verifique as configurações em config/database.php</div>\n";
}

// Teste das classes
echo "<h2>2. Teste das Classes do Sistema</h2>\n";

$classes = [
    'Database' => 'Classe de conexão com banco',
    'Controller' => 'Classe base dos controllers',
    'Assistant' => 'Modelo de assistente',
    'Conversation' => 'Modelo de conversa',
    'Message' => 'Modelo de mensagem',
    'AssistantController' => 'Controller de assistentes',
    'ConversationController' => 'Controller de conversas'
];

foreach ($classes as $className => $description) {
    if (class_exists($className)) {
        echo "<div class='success'>✅ {$className}: {$description}</div>\n";
    } else {
        echo "<div class='error'>❌ {$className}: Classe não encontrada</div>\n";
    }
}

// Teste de dados de exemplo
echo "<h2>3. Dados de Exemplo</h2>\n";
try {
    $assistants = Assistant::findAll($db);
    echo "<div class='success'>✅ Encontrados " . count($assistants) . " assistentes</div>\n";
    
    if (!empty($assistants)) {
        echo "<div class='info'><strong>Assistentes cadastrados:</strong><br>\n";
        foreach ($assistants as $assistant) {
            echo "• " . htmlspecialchars($assistant->getName()) . "<br>\n";
        }
        echo "</div>\n";
    }
    
    $conversations = Conversation::findAll($db);
    echo "<div class='success'>✅ Encontradas " . count($conversations) . " conversas</div>\n";
    
    $messages = Message::findRecent(10, $db);
    echo "<div class='success'>✅ Encontradas " . count($messages) . " mensagens recentes</div>\n";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Erro ao carregar dados: {$e->getMessage()}</div>\n";
}

// Informações do sistema
echo "<h2>4. Informações do Sistema</h2>\n";
echo "<div class='info'>\n";
echo "<strong>Versão:</strong> " . SITE_VERSION . "<br>\n";
echo "<strong>Timezone:</strong> " . TIMEZONE . "<br>\n";
echo "<strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "<br>\n";
echo "<strong>PHP:</strong> " . PHP_VERSION . "<br>\n";
echo "<strong>Base URL:</strong> " . BASE_URL . "<br>\n";
echo "</div>\n";

// Links de navegação
echo "<h2>5. Acesso ao Sistema</h2>\n";
echo "<div class='info'>\n";
echo "Use os links abaixo para acessar o painel administrativo:\n";
echo "</div>\n";

echo "<a href='" . BASE_URL . "/dashboard' class='btn'>🏠 Dashboard</a>\n";
echo "<a href='" . BASE_URL . "/assistants' class='btn'>🤖 Assistentes</a>\n";
echo "<a href='" . BASE_URL . "/conversations' class='btn'>💬 Conversas</a>\n";
echo "<a href='" . BASE_URL . "/messages' class='btn'>📧 Mensagens</a>\n";
echo "<a href='" . BASE_URL . "/stats' class='btn'>📊 Estatísticas</a>\n";

// Estrutura de arquivos
echo "<h2>6. Estrutura de Arquivos</h2>\n";
echo "<pre>\n";
echo "assistant-admin/\n";
echo "├── config/\n";
echo "│   ├── config.php (" . (file_exists(__DIR__ . '/config/config.php') ? '✅' : '❌') . ")\n";
echo "│   └── database.php (" . (file_exists(__DIR__ . '/config/database.php') ? '✅' : '❌') . ")\n";
echo "├── src/\n";
echo "│   ├── Database.php (" . (file_exists(__DIR__ . '/src/Database.php') ? '✅' : '❌') . ")\n";
echo "│   ├── Controller.php (" . (file_exists(__DIR__ . '/src/Controller.php') ? '✅' : '❌') . ")\n";
echo "│   ├── Assistant.php (" . (file_exists(__DIR__ . '/src/Assistant.php') ? '✅' : '❌') . ")\n";
echo "│   ├── Conversation.php (" . (file_exists(__DIR__ . '/src/Conversation.php') ? '✅' : '❌') . ")\n";
echo "│   ├── Message.php (" . (file_exists(__DIR__ . '/src/Message.php') ? '✅' : '❌') . ")\n";
echo "│   ├── AssistantController.php (" . (file_exists(__DIR__ . '/src/AssistantController.php') ? '✅' : '❌') . ")\n";
echo "│   └── ConversationController.php (" . (file_exists(__DIR__ . '/src/ConversationController.php') ? '✅' : '❌') . ")\n";
echo "├── painel/\n";
echo "│   ├── index.php (" . (file_exists(__DIR__ . '/painel/index.php') ? '✅' : '❌') . ")\n";
echo "│   ├── layout.php (" . (file_exists(__DIR__ . '/painel/layout.php') ? '✅' : '❌') . ")\n";
echo "│   ├── .htaccess (" . (file_exists(__DIR__ . '/painel/.htaccess') ? '✅' : '❌') . ")\n";
echo "│   └── views/ (" . (is_dir(__DIR__ . '/painel/views') ? '✅' : '❌') . ")\n";
echo "├── assets/\n";
echo "│   ├── css/admin.css (" . (file_exists(__DIR__ . '/assets/css/admin.css') ? '✅' : '❌') . ")\n";
echo "│   └── js/admin.js (" . (file_exists(__DIR__ . '/assets/js/admin.js') ? '✅' : '❌') . ")\n";
echo "├── database.sql (" . (file_exists(__DIR__ . '/database.sql') ? '✅' : '❌') . ")\n";
echo "├── README.md (" . (file_exists(__DIR__ . '/README.md') ? '✅' : '❌') . ")\n";
echo "└── demo.php (" . (file_exists(__DIR__ . '/demo.php') ? '✅' : '❌') . ")\n";
echo "</pre>\n";

// Instruções de próximos passos
echo "<h2>7. Próximos Passos</h2>\n";
echo "<div class='info'>\n";
echo "<strong>Se tudo estiver funcionando:</strong><br>\n";
echo "1. Acesse o painel administrativo usando os links acima<br>\n";
echo "2. Explore as funcionalidades de CRUD<br>\n";
echo "3. Teste a criação de novos assistentes<br>\n";
echo "4. Visualize as conversas e mensagens de exemplo<br><br>\n";

echo "<strong>Se houver problemas:</strong><br>\n";
echo "1. Verifique se o banco de dados foi criado e populado<br>\n";
echo "2. Confirme as configurações em config/database.php<br>\n";
echo "3. Verifique se o servidor web está configurado corretamente<br>\n";
echo "4. Consulte o arquivo README.md para instruções detalhadas<br>\n";
echo "</div>\n";

echo "<div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;'>\n";
echo "<p style='color: #6b7280;'>Sistema de Administração de Assistentes - Versão " . SITE_VERSION . "</p>\n";
echo "</div>\n";

echo "</div>\n";
echo "</body>\n";
echo "</html>\n";
?>

