<?php

class Message {
    private $db;
    private $id;
    private $conversationId;
    private $senderId;
    private $recipientId;
    private $content;
    private $timestamp;

    public function __construct(Database $database = null) {
        $this->db = $database ?: new Database();
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getConversationId(): ?int {
        return $this->conversationId;
    }

    public function getSenderId(): ?int {
        return $this->senderId;
    }

    public function getRecipientId(): ?int {
        return $this->recipientId;
    }

    public function getContent(): ?string {
        return $this->content;
    }

    public function getTimestamp(): ?string {
        return $this->timestamp;
    }

    // Setters
    public function setConversationId(int $conversationId): self {
        $this->conversationId = $conversationId;
        return $this;
    }

    public function setSenderId(int $senderId): self {
        $this->senderId = $senderId;
        return $this;
    }

    public function setRecipientId(int $recipientId): self {
        $this->recipientId = $recipientId;
        return $this;
    }

    public function setContent(string $content): self {
        $this->content = $content;
        return $this;
    }

    public function setTimestamp(string $timestamp): self {
        $this->timestamp = $timestamp;
        return $this;
    }

    // Métodos de persistência
    public function save(): bool {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create(): bool {
        if (!$this->timestamp) {
            $this->timestamp = date('Y-m-d H:i:s');
        }

        $sql = "INSERT INTO messages (conversation_id, sender_id, recipient_id, content, timestamp) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $this->conversationId,
            $this->senderId,
            $this->recipientId,
            $this->content,
            $this->timestamp
        ];
        
        if ($this->db->execute($sql, $params)) {
            $this->id = (int)$this->db->lastInsertId();
            return true;
        }
        return false;
    }

    private function update(): bool {
        $sql = "UPDATE messages SET conversation_id = ?, sender_id = ?, recipient_id = ?, content = ?, timestamp = ? WHERE id = ?";
        $params = [
            $this->conversationId,
            $this->senderId,
            $this->recipientId,
            $this->content,
            $this->timestamp,
            $this->id
        ];
        
        return $this->db->execute($sql, $params);
    }

    public function delete(): bool {
        if (!$this->id) {
            return false;
        }

        $sql = "DELETE FROM messages WHERE id = ?";
        return $this->db->execute($sql, [$this->id]);
    }

    // Métodos estáticos para busca
    public static function findById(int $id, Database $database = null): ?self {
        $db = $database ?: new Database();
        $data = $db->fetchOne("SELECT * FROM messages WHERE id = ?", [$id]);
        
        if (!$data) {
            return null;
        }

        $message = new self($db);
        $message->loadFromArray($data);
        return $message;
    }

    public static function findByConversation(int $conversationId, Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll(
            "SELECT * FROM messages WHERE conversation_id = ? ORDER BY timestamp ASC",
            [$conversationId]
        );
        
        $messages = [];
        foreach ($results as $data) {
            $message = new self($db);
            $message->loadFromArray($data);
            $messages[] = $message;
        }
        
        return $messages;
    }

    public static function findBySender(int $senderId, Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll(
            "SELECT * FROM messages WHERE sender_id = ? ORDER BY timestamp DESC",
            [$senderId]
        );
        
        $messages = [];
        foreach ($results as $data) {
            $message = new self($db);
            $message->loadFromArray($data);
            $messages[] = $message;
        }
        
        return $messages;
    }

    public static function findByRecipient(int $recipientId, Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll(
            "SELECT * FROM messages WHERE recipient_id = ? ORDER BY timestamp DESC",
            [$recipientId]
        );
        
        $messages = [];
        foreach ($results as $data) {
            $message = new self($db);
            $message->loadFromArray($data);
            $messages[] = $message;
        }
        
        return $messages;
    }

    public static function findRecent(int $limit = 50, Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll(
            "SELECT * FROM messages ORDER BY timestamp DESC LIMIT ?",
            [$limit]
        );
        
        $messages = [];
        foreach ($results as $data) {
            $message = new self($db);
            $message->loadFromArray($data);
            $messages[] = $message;
        }
        
        return $messages;
    }

    public static function count(Database $database = null): int {
        $db = $database ?: new Database();
        $result = $db->fetchOne("SELECT COUNT(*) as count FROM messages");
        return (int)$result['count'];
    }

    public static function countByConversation(int $conversationId, Database $database = null): int {
        $db = $database ?: new Database();
        $result = $db->fetchOne(
            "SELECT COUNT(*) as count FROM messages WHERE conversation_id = ?",
            [$conversationId]
        );
        return (int)$result['count'];
    }

    // Métodos auxiliares
    private function loadFromArray(array $data): void {
        $this->id = (int)$data['id'];
        $this->conversationId = (int)$data['conversation_id'];
        $this->senderId = (int)$data['sender_id'];
        $this->recipientId = (int)$data['recipient_id'];
        $this->content = $data['content'];
        $this->timestamp = $data['timestamp'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversationId,
            'sender_id' => $this->senderId,
            'recipient_id' => $this->recipientId,
            'content' => $this->content,
            'timestamp' => $this->timestamp
        ];
    }

    // Validação
    public function validate(): array {
        $errors = [];

        if (empty($this->conversationId)) {
            $errors['conversation_id'] = 'A conversa é obrigatória.';
        }

        if (empty($this->senderId)) {
            $errors['sender_id'] = 'O remetente é obrigatório.';
        }

        if (empty($this->recipientId)) {
            $errors['recipient_id'] = 'O destinatário é obrigatório.';
        }

        if ($this->senderId === $this->recipientId) {
            $errors['participants'] = 'O remetente e destinatário devem ser diferentes.';
        }

        if (empty($this->content)) {
            $errors['content'] = 'O conteúdo da mensagem é obrigatório.';
        }

        return $errors;
    }

    public function isValid(): bool {
        return empty($this->validate());
    }

    // Métodos específicos da mensagem
    public function getSender(): ?Assistant {
        if (!$this->senderId) {
            return null;
        }

        return Assistant::findById($this->senderId, $this->db);
    }

    public function getRecipient(): ?Assistant {
        if (!$this->recipientId) {
            return null;
        }

        return Assistant::findById($this->recipientId, $this->db);
    }

    public function getConversation(): ?Conversation {
        if (!$this->conversationId) {
            return null;
        }

        return Conversation::findById($this->conversationId, $this->db);
    }

    public function getFormattedTimestamp(string $format = 'd/m/Y H:i:s'): ?string {
        if (!$this->timestamp) {
            return null;
        }

        $date = new DateTime($this->timestamp);
        return $date->format($format);
    }

    public function getTimeAgo(): string {
        if (!$this->timestamp) {
            return 'Desconhecido';
        }

        $now = new DateTime();
        $messageTime = new DateTime($this->timestamp);
        $diff = $now->diff($messageTime);

        if ($diff->days > 0) {
            return $diff->days . ' dia' . ($diff->days > 1 ? 's' : '') . ' atrás';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
        } else {
            return 'Agora mesmo';
        }
    }

    public function getPreview(int $length = 100): string {
        if (!$this->content) {
            return '';
        }

        if (strlen($this->content) <= $length) {
            return $this->content;
        }

        return substr($this->content, 0, $length) . '...';
    }

    public function getWordCount(): int {
        if (!$this->content) {
            return 0;
        }

        return str_word_count($this->content);
    }

    public function getCharacterCount(): int {
        if (!$this->content) {
            return 0;
        }

        return strlen($this->content);
    }

    // Métodos estáticos para estatísticas
    public static function getMessageStats(Database $database = null): array {
        $db = $database ?: new Database();
        
        $totalMessages = self::count($db);
        $todayMessages = $db->fetchOne(
            "SELECT COUNT(*) as count FROM messages WHERE DATE(timestamp) = CURDATE()"
        );
        $weekMessages = $db->fetchOne(
            "SELECT COUNT(*) as count FROM messages WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        );
        $monthMessages = $db->fetchOne(
            "SELECT COUNT(*) as count FROM messages WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );

        return [
            'total' => $totalMessages,
            'today' => (int)$todayMessages['count'],
            'week' => (int)$weekMessages['count'],
            'month' => (int)$monthMessages['count']
        ];
    }
}

