<?php

class ConversationController extends Controller {
    
    public function index(): void {
        try {
            $page = $this->sanitizeInt($_GET["page"] ?? 1);
            $status = $this->sanitizeString($_GET["status"] ?? "");
            $assistant = $this->sanitizeInt($_GET["assistant"] ?? 0);
            $perPage = 15;
            
            // Construir query com filtros
            $whereConditions = [];
            $params = [];
            
            if (!empty($status)) {
                $whereConditions[] = "status = ?";
                $params[] = $status;
            }
            
            if ($assistant > 0) {
                $whereConditions[] = "(assistant1_id = ? OR assistant2_id = ?)";
                $params[] = $assistant;
                $params[] = $assistant;
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
            
            // Contar total
            $countSql = "SELECT COUNT(*) as count FROM conversations {$whereClause}";
            $totalResult = $this->db->fetchOne($countSql, $params);
            $total = (int)$totalResult["count"];
            
            // Buscar conversas
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM conversations {$whereClause} ORDER BY start_time DESC LIMIT {$perPage} OFFSET {$offset}";
            $results = $this->db->fetchAll($sql, $params);
            
            $conversations = [];
            foreach ($results as $data) {
                $conversation = new Conversation($this->db);
                $conversation->setAssistant1Id($data["assistant1_id"])
                           ->setAssistant2Id($data["assistant2_id"])
                           ->setStartTime($data["start_time"])
                           ->setEndTime($data["end_time"])
                           ->setFinalAgreement($data["final_agreement"])
                           ->setStatus($data["status"]);
                // Definir ID manualmente
                $reflection = new ReflectionClass($conversation);
                $idProperty = $reflection->getProperty("id");
                $idProperty->setAccessible(true);
                $idProperty->setValue($conversation, $data["id"]);
                $conversations[] = $conversation;
            }
            
            $pagination = $this->paginate($total, $perPage, $page);
            
            // Buscar assistentes para filtro
            $assistants = Assistant::findAll($this->db);
            
            $this->renderLayout("conversations/index", [
                "conversations" => $conversations,
                "pagination" => $pagination,
                "assistants" => $assistants,
                "filters" => [
                    "status" => $status,
                    "assistant" => $assistant
                ],
                "total" => $total
            ]);
            
        } catch (Exception $e) {
            $this->addError("Erro ao carregar conversas: " . $e->getMessage());
            $this->renderLayout("conversations/index", [
                "conversations" => [],
                "pagination" => $this->paginate(0, 15, 1),
                "assistants" => [],
                "filters" => [],
                "total" => 0
            ]);
        }
    }
    
    public function show(): void {
        try {
            $id = $this->sanitizeInt($_GET["id"] ?? 0);
            $conversation = Conversation::findById($id, $this->db);
            
            if (!$conversation) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Conversa não encontrada.");
                return;
            }
            
            $messages = $conversation->getMessages();
            $assistant1 = $conversation->getAssistant1();
            $assistant2 = $conversation->getAssistant2();
            
            $this->renderLayout("conversations/show", [
                "conversation" => $conversation,
                "messages" => $messages,
                "assistant1" => $assistant1,
                "assistant2" => $assistant2,
                "message_count" => count($messages)
            ]);
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Erro ao carregar conversa: " . $e->getMessage());
        }
    }
    
    public function updateStatus(): void {
        try {
            if (!$this->isPost()) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Método não permitido.");
                return;
            }
            
            $data = $this->getPostData();
            
            // Verificar CSRF token
            if (!$this->verifyCsrfToken($data["csrf_token"] ?? "")) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Token de segurança inválido.");
                return;
            }
            
            $id = $this->sanitizeInt($data["id"] ?? 0);
            $status = $this->sanitizeString($data["status"] ?? "");
            $finalAgreement = $this->sanitizeString($data["final_agreement"] ?? "");
            
            $conversation = Conversation::findById($id, $this->db);
            
            if (!$conversation) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Conversa não encontrada.");
                return;
            }
            
            if ($status === Conversation::STATUS_COMPLETED) {
                $conversation->complete($finalAgreement);
                $this->redirectWithSuccess(BASE_URL . "/index.php?route=conversations&action=show&id=" . $id, "Conversa marcada como concluída!");
            } elseif ($status === Conversation::STATUS_FAILED) {
                $conversation->fail();
                $this->redirectWithSuccess(BASE_URL . "/index.php?route=conversations&action=show&id=" . $id, "Conversa marcada como falhou!");
            } else {
                $conversation->setStatus($status);
                $conversation->save();
                $this->redirectWithSuccess(BASE_URL . "/index.php?route=conversations&action=show&id=" . $id, "Status da conversa atualizado!");
            }
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Erro ao atualizar status: " . $e->getMessage());
        }
    }
    
    public function delete(): void {
        try {
            if (!$this->isPost()) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Método não permitido.");
                return;
            }
            
            $data = $this->getPostData();
            
            // Verificar CSRF token
            if (!$this->verifyCsrfToken($data["csrf_token"] ?? "")) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Token de segurança inválido.");
                return;
            }
            
            $id = $this->sanitizeInt($data["id"] ?? 0);
            $conversation = Conversation::findById($id, $this->db);
            
            if (!$conversation) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Conversa não encontrada.");
                return;
            }
            
            if ($conversation->delete()) {
                $this->redirectWithSuccess(BASE_URL . "/index.php?route=conversations", "Conversa excluída com sucesso!");
            } else {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Erro ao excluir a conversa.");
            }
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Erro ao excluir conversa: " . $e->getMessage());
        }
    }
    
    // Método para estatísticas
    public function stats(): void {
        try {
            $totalConversations = Conversation::count($this->db);
            $inProgressCount = Conversation::countByStatus(Conversation::STATUS_IN_PROGRESS, $this->db);
            $completedCount = Conversation::countByStatus(Conversation::STATUS_COMPLETED, $this->db);
            $failedCount = Conversation::countByStatus(Conversation::STATUS_FAILED, $this->db);
            
            // Estatísticas por período
            $todayStats = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM conversations WHERE DATE(start_time) = CURDATE()"
            );
            
            $weekStats = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM conversations WHERE start_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
            );
            
            $monthStats = $this->db->fetchOne(
                "SELECT COUNT(*) as count FROM conversations WHERE start_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
            );
            
            // Duração média das conversas concluídas
            $avgDurationResult = $this->db->fetchOne(
                "SELECT AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration 
                 FROM conversations 
                 WHERE status = \"completed\" AND end_time IS NOT NULL"
            );
            
            $avgDuration = $avgDurationResult["avg_duration"] ? round($avgDurationResult["avg_duration"], 1) : 0;
            
            $stats = [
                "total" => $totalConversations,
                "in_progress" => $inProgressCount,
                "completed" => $completedCount,
                "failed" => $failedCount,
                "today" => (int)$todayStats["count"],
                "week" => (int)$weekStats["count"],
                "month" => (int)$monthStats["count"],
                "avg_duration" => $avgDuration,
                "success_rate" => $totalConversations > 0 ? round(($completedCount / $totalConversations) * 100, 1) : 0
            ];
            
            if ($this->isAjax()) {
                $this->jsonSuccess("Estatísticas obtidas", $stats);
            } else {
                $this->renderLayout("conversations/stats", ["stats" => $stats]);
            }
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                $this->jsonError("Erro ao obter estatísticas: " . $e->getMessage());
            } else {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Erro ao carregar estatísticas: " . $e->getMessage());
            }
        }
    }
    
    // Método para exportar conversa
    public function export(): void {
        try {
            $id = $this->sanitizeInt($_GET["id"] ?? 0);
            $format = $this->sanitizeString($_GET["format"] ?? "json");
            
            $conversation = Conversation::findById($id, $this->db);
            
            if (!$conversation) {
                $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Conversa não encontrada.");
                return;
            }
            
            $messages = $conversation->getMessages();
            $assistant1 = $conversation->getAssistant1();
            $assistant2 = $conversation->getAssistant2();
            
            $exportData = [
                "conversation" => $conversation->toArray(),
                "assistant1" => $assistant1->toArray(),
                "assistant2" => $assistant2->toArray(),
                "messages" => array_map(function($message) {
                    return $message->toArray();
                }, $messages)
            ];
            
            if ($format === "json") {
                header("Content-Type: application/json");
                header("Content-Disposition: attachment; filename=\"conversa_" . $id . ".json\"");
                echo json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } elseif ($format === "txt") {
                header("Content-Type: text/plain; charset=utf-8");
                header("Content-Disposition: attachment; filename=\"conversa_" . $id . ".txt\"");
                
                echo "CONVERSA #" . $id . "\n";
                echo "=" . str_repeat("=", 50) . "\n\n";
                echo "Assistente 1: " . $assistant1->getName() . "\n";
                echo "Assistente 2: " . $assistant2->getName() . "\n";
                echo "Início: " . $conversation->getStartTime() . "\n";
                echo "Status: " . $conversation->getStatusLabel() . "\n";
                
                if ($conversation->getEndTime()) {
                    echo "Fim: " . $conversation->getEndTime() . "\n";
                    echo "Duração: " . $conversation->getFormattedDuration() . "\n";
                }
                
                echo "\nMENSAGENS:\n";
                echo str_repeat("-", 50) . "\n\n";
                
                foreach ($messages as $message) {
                    $sender = $message->getSender();
                    echo "[" . $message->getTimestamp() . "] " . $sender->getName() . ":\n";
                    echo $message->getContent() . "\n\n";
                }
                
                if ($conversation->getFinalAgreement()) {
                    echo "\nACORDO FINAL:\n";
                    echo str_repeat("-", 50) . "\n";
                    echo $conversation->getFinalAgreement() . "\n";
                }
            }
            
            exit;
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=conversations", "Erro ao exportar conversa: " . $e->getMessage());
        }
    }
}


