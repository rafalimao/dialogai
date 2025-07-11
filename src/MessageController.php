<?php

// Classe simples para Mensagens
class MessageController extends Controller {
    public function index(): void {
        try {
            $page = $this->sanitizeInt($_GET['page'] ?? 1);
            $conversationId = $this->sanitizeInt($_GET['conversation'] ?? 0);
            $perPage = 20;
            
            $messages = [];
            $total = 0;
            
            if ($conversationId > 0) {
                $messages = Message::findByConversation($conversationId, $this->db);
                $total = count($messages);
                
                // Simular paginação
                $offset = ($page - 1) * $perPage;
                $messages = array_slice($messages, $offset, $perPage);
            } else {
                $messages = Message::findRecent(50, $this->db);
                $total = Message::count($this->db);
            }
            
            $pagination = $this->paginate($total, $perPage, $page);
            
            $this->renderLayout('messages/index', [
                'messages' => $messages,
                'pagination' => $pagination,
                'conversation_id' => $conversationId,
                'total' => $total
            ]);
            
        } catch (Exception $e) {
            $this->addError('Erro ao carregar mensagens: ' . $e->getMessage());
            $this->renderLayout('messages/index', [
                'messages' => [],
                'pagination' => $this->paginate(0, 20, 1),
                'conversation_id' => 0,
                'total' => 0
            ]);
        }
    }
    
    public function show(): void {
        try {
            $id = $this->sanitizeInt($_GET['id'] ?? 0);
            $message = Message::findById($id, $this->db);
            
            if (!$message) {
                $this->redirectWithError(BASE_URL . '/messages', 'Mensagem não encontrada.');
                return;
            }
            
            $conversation = $message->getConversation();
            $sender = $message->getSender();
            $recipient = $message->getRecipient();
            
            $this->renderLayout('messages/show', [
                'message' => $message,
                'conversation' => $conversation,
                'sender' => $sender,
                'recipient' => $recipient
            ]);
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . '/messages', 'Erro ao carregar mensagem: ' . $e->getMessage());
        }
    }
    
    public function delete(): void {
        try {
            if (!$this->isPost()) {
                $this->redirectWithError(BASE_URL . '/messages', 'Método não permitido.');
                return;
            }
            
            $data = $this->getPostData();
            
            if (!$this->verifyCsrfToken($data['csrf_token'] ?? '')) {
                $this->redirectWithError(BASE_URL . '/messages', 'Token de segurança inválido.');
                return;
            }
            
            $id = $this->sanitizeInt($data['id'] ?? 0);
            $message = Message::findById($id, $this->db);
            
            if (!$message) {
                $this->redirectWithError(BASE_URL . '/messages', 'Mensagem não encontrada.');
                return;
            }
            
            if ($message->delete()) {
                $this->redirectWithSuccess(BASE_URL . '/messages', 'Mensagem excluída com sucesso!');
            } else {
                $this->redirectWithError(BASE_URL . '/messages', 'Erro ao excluir a mensagem.');
            }
            
        } catch (Exception $e) {
            $this->redirectWithError(BASE_URL . '/messages', 'Erro ao excluir mensagem: ' . $e->getMessage());
        }
    }
}
