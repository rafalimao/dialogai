<?php

class DashboardController extends Controller {
    public function index(): void {
        // Incluir os modelos necessários
        require_once __DIR__ . '/Assistant.php';
        require_once __DIR__ . '/Conversation.php';
        require_once __DIR__ . '/Message.php';

        // Obter estatísticas
        try {
            $totalAssistants = Assistant::count();
            $totalConversations = Conversation::count();
            $activeConversations = Conversation::countByStatus(Conversation::STATUS_IN_PROGRESS);
            $completedConversations = Conversation::countByStatus(Conversation::STATUS_COMPLETED);
            $failedConversations = Conversation::countByStatus(Conversation::STATUS_FAILED);
            $messageStats = Message::getMessageStats();
            
            // Conversas recentes
            $recentConversations = Conversation::findAll(null, 5);
            
            // Assistentes mais ativos
            $db = new Database();
            $activeAssistantsQuery = "
                SELECT a.*, COUNT(c.id) as conversation_count 
                FROM assistants a 
                LEFT JOIN conversations c ON (a.id = c.assistant1_id OR a.id = c.assistant2_id)
                GROUP BY a.id 
                ORDER BY conversation_count DESC 
                LIMIT 5
            ";
            $activeAssistantsData = $db->fetchAll($activeAssistantsQuery);
            
        } catch (Exception $e) {
            // Em caso de erro, definir valores padrão
            $totalAssistants = 0;
            $totalConversations = 0;
            $activeConversations = 0;
            $completedConversations = 0;
            $failedConversations = 0;
            $messageStats = ['total' => 0, 'today' => 0, 'week' => 0, 'month' => 0];
            $recentConversations = [];
            $activeAssistantsData = [];
            // Opcional: logar o erro para depuração
            // error_log("Erro ao carregar dados do dashboard: " . $e->getMessage());
        }

        // Renderizar a view do dashboard
        $this->renderLayout('dashboard/index', [
            'totalAssistants' => $totalAssistants,
            'totalConversations' => $totalConversations,
            'activeConversations' => $activeConversations,
            'completedConversations' => $completedConversations,
            'failedConversations' => $failedConversations,
            'messageStats' => $messageStats,
            'recentConversations' => $recentConversations,
            'activeAssistantsData' => $activeAssistantsData
        ]);
    }
}

