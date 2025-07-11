<?php

class Conversation {
    private $db;
    private $id;
    private $assistant1Id;
    private $assistant2Id;
    private $startTime;
    private $endTime;
    private $finalAgreement;
    private $status;

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    public function __construct(Database $database = null) {
        $this->db = $database ?: new Database();
        $this->status = self::STATUS_IN_PROGRESS;
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getAssistant1Id(): ?int {
        return $this->assistant1Id;
    }

    public function getAssistant2Id(): ?int {
        return $this->assistant2Id;
    }

    public function getStartTime(): ?string {
        return $this->startTime;
    }

    public function getEndTime(): ?string {
        return $this->endTime;
    }

    public function getFinalAgreement(): ?string {
        return $this->finalAgreement;
    }

    public function getStatus(): string {
        return $this->status;
    }

    // Setters
    public function setAssistant1Id(int $assistant1Id): self {
        $this->assistant1Id = $assistant1Id;
        return $this;
    }

    public function setAssistant2Id(int $assistant2Id): self {
        $this->assistant2Id = $assistant2Id;
        return $this;
    }

    public function setStartTime(string $startTime): self {
        $this->startTime = $startTime;
        return $this;
    }

    public function setEndTime(?string $endTime): self {
        $this->endTime = $endTime;
        return $this;
    }

    public function setFinalAgreement(?string $finalAgreement): self {
        $this->finalAgreement = $finalAgreement;
        return $this;
    }

    public function setStatus(string $status): self {
        if (!in_array($status, [self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED, self::STATUS_FAILED])) {
            throw new InvalidArgumentException("Status inválido: {$status}");
        }
        $this->status = $status;
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
        if (!$this->startTime) {
            $this->startTime = date('Y-m-d H:i:s');
        }

        $sql = "INSERT INTO conversations (assistant1_id, assistant2_id, start_time, end_time, final_agreement, status) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $this->assistant1Id,
            $this->assistant2Id,
            $this->startTime,
            $this->endTime,
            $this->finalAgreement,
            $this->status
        ];
        
        if ($this->db->execute($sql, $params)) {
            $this->id = (int)$this->db->lastInsertId();
            return true;
        }
        return false;
    }

    private function update(): bool {
        $sql = "UPDATE conversations SET assistant1_id = ?, assistant2_id = ?, start_time = ?, end_time = ?, final_agreement = ?, status = ? WHERE id = ?";
        $params = [
            $this->assistant1Id,
            $this->assistant2Id,
            $this->startTime,
            $this->endTime,
            $this->finalAgreement,
            $this->status,
            $this->id
        ];
        
        return $this->db->execute($sql, $params);
    }

    public function delete(): bool {
        if (!$this->id) {
            return false;
        }

        // Excluir mensagens relacionadas primeiro
        $this->db->execute("DELETE FROM messages WHERE conversation_id = ?", [$this->id]);

        // Excluir a conversa
        $sql = "DELETE FROM conversations WHERE id = ?";
        return $this->db->execute($sql, [$this->id]);
    }

    // Métodos estáticos para busca
    public static function findById(int $id, Database $database = null): ?self {
        $db = $database ?: new Database();
        $data = $db->fetchOne("SELECT * FROM conversations WHERE id = ?", [$id]);
        
        if (!$data) {
            return null;
        }

        $conversation = new self($db);
        $conversation->loadFromArray($data);
        return $conversation;
    }

    public static function findAll(Database $database = null, int $limit = null, int $offset = 0): array {
        $db = $database ?: new Database();
        $sql = "SELECT * FROM conversations ORDER BY start_time DESC";
        
        if ($limit) {
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        $results = $db->fetchAll($sql);
        
        $conversations = [];
        foreach ($results as $data) {
            $conversation = new self($db);
            $conversation->loadFromArray($data);
            $conversations[] = $conversation;
        }
        
        return $conversations;
    }

    public static function findByAssistant(int $assistantId, Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll(
            "SELECT * FROM conversations WHERE assistant1_id = ? OR assistant2_id = ? ORDER BY start_time DESC",
            [$assistantId, $assistantId]
        );
        
        $conversations = [];
        foreach ($results as $data) {
            $conversation = new self($db);
            $conversation->loadFromArray($data);
            $conversations[] = $conversation;
        }
        
        return $conversations;
    }

    public static function findByStatus(string $status, Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll(
            "SELECT * FROM conversations WHERE status = ? ORDER BY start_time DESC",
            [$status]
        );
        
        $conversations = [];
        foreach ($results as $data) {
            $conversation = new self($db);
            $conversation->loadFromArray($data);
            $conversations[] = $conversation;
        }
        
        return $conversations;
    }

    public static function count(Database $database = null): int {
        $db = $database ?: new Database();
        $result = $db->fetchOne("SELECT COUNT(*) as count FROM conversations");
        return (int)$result['count'];
    }

    public static function countByStatus(string $status, Database $database = null): int {
        $db = $database ?: new Database();
        $result = $db->fetchOne("SELECT COUNT(*) as count FROM conversations WHERE status = ?", [$status]);
        return (int)$result['count'];
    }

    // Métodos auxiliares
    private function loadFromArray(array $data): void {
        $this->id = (int)$data['id'];
        $this->assistant1Id = (int)$data['assistant1_id'];
        $this->assistant2Id = (int)$data['assistant2_id'];
        $this->startTime = $data['start_time'];
        $this->endTime = $data['end_time'];
        $this->finalAgreement = $data['final_agreement'];
        $this->status = $data['status'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'assistant1_id' => $this->assistant1Id,
            'assistant2_id' => $this->assistant2Id,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'final_agreement' => $this->finalAgreement,
            'status' => $this->status
        ];
    }

    // Validação
    public function validate(): array {
        $errors = [];

        if (empty($this->assistant1Id)) {
            $errors['assistant1_id'] = 'O primeiro assistente é obrigatório.';
        }

        if (empty($this->assistant2Id)) {
            $errors['assistant2_id'] = 'O segundo assistente é obrigatório.';
        }

        if ($this->assistant1Id === $this->assistant2Id) {
            $errors['assistants'] = 'Os assistentes devem ser diferentes.';
        }

        if (empty($this->startTime)) {
            $errors['start_time'] = 'A data de início é obrigatória.';
        }

        return $errors;
    }

    public function isValid(): bool {
        return empty($this->validate());
    }

    // Métodos específicos da conversa
    public function complete(string $finalAgreement = null): bool {
        $this->status = self::STATUS_COMPLETED;
        $this->endTime = date('Y-m-d H:i:s');
        $this->finalAgreement = $finalAgreement;
        
        return $this->save();
    }

    public function fail(): bool {
        $this->status = self::STATUS_FAILED;
        $this->endTime = date('Y-m-d H:i:s');
        
        return $this->save();
    }

    public function getDuration(): ?int {
        if (!$this->startTime || !$this->endTime) {
            return null;
        }

        $start = new DateTime($this->startTime);
        $end = new DateTime($this->endTime);
        
        return $end->getTimestamp() - $start->getTimestamp();
    }

    public function getFormattedDuration(): ?string {
        $duration = $this->getDuration();
        if ($duration === null) {
            return null;
        }

        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $seconds = $duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
    }

    public function getMessages(): array {
        if (!$this->id) {
            return [];
        }

        return Message::findByConversation($this->id, $this->db);
    }

    public function getMessageCount(): int {
        if (!$this->id) {
            return 0;
        }

        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM messages WHERE conversation_id = ?",
            [$this->id]
        );
        
        return (int)$result['count'];
    }

    public function getAssistant1(): ?Assistant {
        if (!$this->assistant1Id) {
            return null;
        }

        return Assistant::findById($this->assistant1Id, $this->db);
    }

    public function getAssistant2(): ?Assistant {
        if (!$this->assistant2Id) {
            return null;
        }

        return Assistant::findById($this->assistant2Id, $this->db);
    }

    public function getStatusLabel(): string {
        switch ($this->status) {
            case self::STATUS_IN_PROGRESS:
                return 'Em Progresso';
            case self::STATUS_COMPLETED:
                return 'Concluída';
            case self::STATUS_FAILED:
                return 'Falhou';
            default:
                return 'Desconhecido';
        }
    }

    public function getStatusBadgeClass(): string {
        switch ($this->status) {
            case self::STATUS_IN_PROGRESS:
                return 'badge-warning';
            case self::STATUS_COMPLETED:
                return 'badge-success';
            case self::STATUS_FAILED:
                return 'badge-danger';
            default:
                return 'badge-info';
        }
    }
}

