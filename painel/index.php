<?php
/**
 * Sistema de Administração de Assistentes
 * Arquivo principal de roteamento do painel administrativo
 */

// Incluir configurações
require_once __DIR__ . 
'/../config/config.php';

// Definir rota atual a partir de parâmetros GET
$controller = $_GET['route'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Mapeamento de rotas
$routes = [
    // Dashboard
    'dashboard' => [
        'controller' => 'DashboardController',
        'actions' => ['index']
    ],
    
    // Assistentes
    'assistants' => [
        'controller' => 'AssistantController',
        'actions' => [
            'index' => 'index',
            'create' => 'create',
            'edit' => 'edit',
            'delete' => 'delete',
            'search' => 'search',
            'stats' => 'stats'
        ]
    ],
    
    // Conversas
    'conversations' => [
        'controller' => 'ConversationController',
        'actions' => [
            'index' => 'index',
            'show' => 'show',
            'update-status' => 'updateStatus',
            'delete' => 'delete',
            'export' => 'export',
            'stats' => 'stats'
        ]
    ],
    
    // Mensagens
    'messages' => [
        'controller' => 'MessageController',
        'actions' => [
            'index' => 'index',
            'show' => 'show',
            'delete' => 'delete'
        ]
    ],
    
    // Estatísticas
    'stats' => [
        'controller' => 'StatsController',
        'actions' => ['index']
    ]
];

try {
    // Verificar se a rota existe
    if (!isset($routes[$controller])) {
        throw new Exception("Página não encontrada", 404);
    }
    
    $routeConfig = $routes[$controller];
    $controllerClass = $routeConfig['controller'];
    
    // Verificar se o controller existe
    if (!class_exists($controllerClass)) {
        throw new Exception("Controller não encontrado: {$controllerClass}", 500);
    }
    
    // Instanciar controller
    $controllerInstance = new $controllerClass();
    
    // Determinar método a ser chamado
    $method = 'index';
    
    if (isset($routeConfig['actions'])) {
        if (is_array($routeConfig['actions'])) {
            if (isset($routeConfig['actions'][$action])) {
                $method = $routeConfig['actions'][$action];
            } elseif (in_array($action, $routeConfig['actions'])) {
                $method = $action;
            }
        } else {
            $method = $routeConfig['actions'];
        }
    }
    
    // Rotas especiais com ID
    if ($id && is_numeric($id)) {
        $_GET['id'] = $id;
    }
    
    // Verificar se o método existe
    if (!method_exists($controllerInstance, $method)) {
        throw new Exception("Método não encontrado: {$method}", 500);
    }
    
    // Executar método do controller
    $controllerInstance->$method();
    
} catch (Exception $e) {
    // Tratar erros
    $errorCode = $e->getCode() ?: 500;
    $errorMessage = $e->getMessage();
    
    // Log do erro (em produção, usar um sistema de log adequado)
    error_log("Erro no painel administrativo: {$errorMessage} - URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
    
    // Exibir página de erro
    http_response_code($errorCode);
    
    $page_title = 'Erro ' . $errorCode;
    $page_subtitle = 'Ocorreu um problema ao processar sua solicitação';
    
    include __DIR__ . '/layout.php';
    ?>
    
    <div class="fade-in">
        <div class="card">
            <div class="card-body text-center" style="padding: 3rem;">
                <i class="fas fa-exclamation-triangle" 
                   style="font-size: 4rem; color: var(--danger-color); margin-bottom: 1rem;"></i>
                
                <h1 style="font-size: 3rem; font-weight: 700; color: var(--danger-color); margin-bottom: 1rem;">
                    <?= $errorCode ?>
                </h1>
                
                <h3 class="mb-3">
                    <?php
                    switch ($errorCode) {
                        case 404:
                            echo 'Página Não Encontrada';
                            break;
                        case 403:
                            echo 'Acesso Negado';
                            break;
                        case 500:
                            echo 'Erro Interno do Servidor';
                            break;
                        default:
                            echo 'Erro';
                    }
                    ?>
                </h3>
                
                <p class="text-muted mb-4">
                    <?php
                    switch ($errorCode) {
                        case 404:
                            echo 'A página que você está procurando não foi encontrada.';
                            break;
                        case 403:
                            echo 'Você não tem permissão para acessar esta página.';
                            break;
                        case 500:
                            echo 'Ocorreu um erro interno no servidor. Tente novamente mais tarde.';
                            break;
                        default:
                            echo htmlspecialchars($errorMessage);
                    }
                    ?>
                </p>
                
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= BASE_URL ?>/index.php?route=dashboard" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        Ir para Dashboard
                    </a>
                    
                    <button onclick="history.back()" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i>
                        Voltar
                    </button>
                </div>
                
                <?php if ($errorCode >= 500): ?>
                    <div class="mt-4">
                        <small class="text-muted">
                            Se o problema persistir, entre em contato com o administrador do sistema.
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php
    exit;
}