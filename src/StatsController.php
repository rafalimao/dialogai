<?php

// Classe para Estatísticas
class StatsController extends Controller {
    public function index(): void {
        try {
            $totalAssistants = Assistant::count($this->db);
            $totalConversations = Conversation::count($this->db);
            $inProgressCount = Conversation::countByStatus(Conversation::STATUS_IN_PROGRESS, $this->db);
            $completedCount = Conversation::countByStatus(Conversation::STATUS_COMPLETED, $this->db);
            $failedCount = Conversation::countByStatus(Conversation::STATUS_FAILED, $this->db);
            $messageStats = Message::getMessageStats($this->db);
            
            // Estatísticas por período
            $todayConversations = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM conversations WHERE DATE(start_time) = CURDATE()"
            );
            
            $weekConversations = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM conversations WHERE start_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
            );
            
            $monthConversations = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM conversations WHERE start_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
            );
            
            // Duração média
            $avgDurationResult = $this->db->fetchOne(
                "SELECT AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration 
                 FROM conversations 
                 WHERE status = 'completed' AND end_time IS NOT NULL"
            );
            
            $stats = [
                'assistants' => $totalAssistants,
                'conversations' => [
                    'total' => $totalConversations,
                    'in_progress' => $inProgressCount,
                    'completed' => $completedCount,
                    'failed' => $failedCount,
                    'today' => (int)$todayConversations['count'],
                    'week' => (int)$weekConversations['count'],
                    'month' => (int)$monthConversations['count']
                ],
                'messages' => $messageStats,
                'avg_duration' => $avgDurationResult['avg_duration'] ? round($avgDurationResult['avg_duration'], 1) : 0,
                'success_rate' => $totalConversations > 0 ? round(($completedCount / $totalConversations) * 100, 1) : 0
            ];
            
            $this->renderLayout('stats/index', ['stats' => $stats]);
            
        } catch (Exception $e) {
            $this->addError('Erro ao carregar estatísticas: ' . $e->getMessage());
            $this->renderLayout('stats/index', ['stats' => []]);
        }
    }
}
