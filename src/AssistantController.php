<?php

class AssistantController extends Controller {
    
    public function index(): void {
        try {
            $page = $this->sanitizeInt($_GET["page"] ?? 1);
            $search = $this->sanitizeString($_GET["search"] ?? "");
            $perPage = 10;
            
            if (!empty($search)) {
                $assistants = Assistant::findByName($search, $this->db);
                $total = count($assistants);
                // Simular paginação para busca
                $offset = ($page - 1) * $perPage;
                $assistants = array_slice($assistants, $offset, $perPage);
            } else {
                $total = Assistant::count($this->db);
                $assistants = Assistant::findAll($this->db);
                // Simular paginação (idealmente seria implementada na query)
                $offset = ($page - 1) * $perPage;
                $assistants = array_slice($assistants, $offset, $perPage);
            }
            
            $pagination = $this->paginate($total, $perPage, $page);
            
            $this->renderLayout("assistants/index", [
                "assistants" => $assistants,
                "pagination" => $pagination,
                "search" => $search,
                "total" => $total
            ]);
            
        } catch (Exception $e) {
            $this->addError("Erro ao carregar assistentes: " . $e->getMessage());
            $this->renderLayout("assistants/index", [
                "assistants" => [],
                "pagination" => $this->paginate(0, 10, 1),
                "search" => "",
                "total" => 0
            ]);
        }
    }
    
    public function create(): void {
        if ($this->isPost()) {
            $this->store();
            return;
        }
        
        $this->renderLayout("assistants/create", [
            "assistant" => new Assistant(),
            "csrf_token" => $this->generateCsrfToken()
        ]);
    }
    
    public function store(): void {
        try {
            $data = $this->getPostData();
            
            // Verificar CSRF token
            if (!$this->verifyCsrfToken($data["csrf_token"] ?? "")) {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants&action=create", "Token de segurança inválido.");
                return;
            }
            
            $assistant = new Assistant($this->db);
            $assistant->setName($this->sanitizeString($data["name"] ?? ""))
                     ->setProfile($this->sanitizeString($data["profile"] ?? ""))
                     ->setInitialPrompt($this->sanitizeString($data["initial_prompt"] ?? ""))
                     ->setGoal($this->sanitizeString($data["goal"] ?? ""));
            
            $errors = $assistant->validate();
            if (!empty($errors)) {
                $_SESSION["form_data"] = $data;
                $_SESSION["form_errors"] = $errors;
                $this->redirect(BASE_URL . "/index.php?route=assistants&action=create");
                return;
            }
            
            if ($assistant->save()) {
                $this->redirectWithSuccess(BASE_URL . "/index.php?route=assistants", "Assistente criado com sucesso!");
            } else {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants&action=create", "Erro ao salvar o assistente.");
            }
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=assistants&action=create", "Erro interno: " . $e->getMessage());
        }
    }
    
    public function show(): void {
        try {
            $id = $this->sanitizeInt($_GET["id"] ?? 0);
            $assistant = Assistant::findById($id, $this->db);
            
            if (!$assistant) {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Assistente não encontrado.");
                return;
            }
            
            $conversations = Conversation::findByAssistant($id, $this->db);
            $conversationCount = $assistant->getConversationCount();
            $lastConversation = $assistant->getLastConversationDate();
            
            $this->renderLayout("assistants/show", [
                "assistant" => $assistant,
                "conversations" => array_slice($conversations, 0, 5), // Últimas 5 conversas
                "conversation_count" => $conversationCount,
                "last_conversation" => $lastConversation
            ]);
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Erro ao carregar assistente: " . $e->getMessage());
        }
    }
    
    public function edit(): void {
        try {
            $id = $this->sanitizeInt($_GET["id"] ?? 0);
            $assistant = Assistant::findById($id, $this->db);
            
            if (!$assistant) {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Assistente não encontrado.");
                return;
            }
            
            if ($this->isPost()) {
                $this->update($assistant);
                return;
            }
            
            // Recuperar dados do formulário se houver erro
            $formData = $_SESSION["form_data"] ?? null;
            $formErrors = $_SESSION["form_errors"] ?? [];
            unset($_SESSION["form_data"], $_SESSION["form_errors"]);
            
            $this->renderLayout("assistants/edit", [
                "assistant" => $assistant,
                "form_data" => $formData,
                "form_errors" => $formErrors,
                "csrf_token" => $this->generateCsrfToken()
            ]);
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Erro ao carregar assistente: " . $e->getMessage());
        }
    }
    
    public function update(Assistant $assistant): void {
        try {
            $data = $this->getPostData();
            
            // Verificar CSRF token
            if (!$this->verifyCsrfToken($data["csrf_token"] ?? "")) {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants&action=edit&id=" . $assistant->getId(), "Token de segurança inválido.");
                return;
            }
            
            $assistant->setName($this->sanitizeString($data["name"] ?? ""))
                     ->setProfile($this->sanitizeString($data["profile"] ?? ""))
                     ->setInitialPrompt($this->sanitizeString($data["initial_prompt"] ?? ""))
                     ->setGoal($this->sanitizeString($data["goal"] ?? ""));
            
            $errors = $assistant->validate();
            if (!empty($errors)) {
                $_SESSION["form_data"] = $data;
                $_SESSION["form_errors"] = $errors;
                $this->redirect(BASE_URL . "/index.php?route=assistants&action=edit&id=" . $assistant->getId());
                return;
            }
            
            if ($assistant->save()) {
                $this->redirectWithSuccess(BASE_URL . "/index.php?route=assistants", "Assistente atualizado com sucesso!");
            } else {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants&action=edit&id=" . $assistant->getId(), "Erro ao atualizar o assistente.");
            }
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=assistants&action=edit&id=" . $assistant->getId(), "Erro interno: " . $e->getMessage());
        }
    }
    
    public function delete(): void {
        try {
            if (!$this->isPost()) {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Método não permitido.");
                return;
            }
            
            $data = $this->getPostData();
            
            // Verificar CSRF token
            if (!$this->verifyCsrfToken($data["csrf_token"] ?? "")) {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Token de segurança inválido.");
                return;
            }
            
            $id = $this->sanitizeInt($data["id"] ?? 0);
            $assistant = Assistant::findById($id, $this->db);
            
            if (!$assistant) {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Assistente não encontrado.");
                return;
            }
            
            if ($assistant->delete()) {
                $this->redirectWithSuccess(BASE_URL . "/index.php?route=assistants", "Assistente excluído com sucesso!");
            } else {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Erro ao excluir o assistente.");
            }
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Erro ao excluir assistente: " . $e->getMessage());
        }
    }
    
    // Método para AJAX - busca de assistentes
    public function search(): void {
        try {
            if (!$this->isAjax()) {
                $this->jsonError("Requisição inválida", [], 405);
                return;
            }
            
            $query = $this->sanitizeString($_GET["q"] ?? "");
            
            if (strlen($query) < 2) {
                $this->jsonSuccess("Busca realizada", []);
                return;
            }
            
            $assistants = Assistant::findByName($query, $this->db);
            $results = [];
            
            foreach ($assistants as $assistant) {
                $results[] = [
                    "id" => $assistant->getId(),
                    "name" => $assistant->getName(),
                    "profile" => $assistant->getProfile()
                ];
            }
            
            $this->jsonSuccess("Busca realizada", $results);
            
        } catch (Exception $e) {
            $this->jsonError("Erro na busca: " . $e->getMessage());
        }
    }
    
    // Método para obter estatísticas
    public function stats(): void {
        try {
            $totalAssistants = Assistant::count($this->db);
            $totalConversations = Conversation::count($this->db);
            $activeConversations = Conversation::countByStatus(Conversation::STATUS_IN_PROGRESS, $this->db);
            $completedConversations = Conversation::countByStatus(Conversation::STATUS_COMPLETED, $this->db);
            
            $stats = [
                "total_assistants" => $totalAssistants,
                "total_conversations" => $totalConversations,
                "active_conversations" => $activeConversations,
                "completed_conversations" => $completedConversations
            ];
            
            if ($this->isAjax()) {
                $this->jsonSuccess("Estatísticas obtidas", $stats);
            } else {
                $this->renderLayout("assistants/stats", ["stats" => $stats]);
            }
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                $this->jsonError("Erro ao obter estatísticas: " . $e->getMessage());
            } else {
                $this->redirectWithError(BASE_URL . "/index.php?route=assistants", "Erro ao carregar estatísticas: " . $e->getMessage());
            }
        }
    }
}


