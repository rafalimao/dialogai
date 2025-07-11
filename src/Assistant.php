<?php

class Assistant {
    private $db;
    private $id;
    private $name;
    private $profile;
    private $initialPrompt;
    private $goal;

    public function __construct(Database $database = null) {
        $this->db = $database ?: new Database();
    }

    // Getters
    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getProfile(): ?string {
        return $this->profile;
    }

    public function getInitialPrompt(): ?string {
        return $this->initialPrompt;
    }

    public function getGoal(): ?string {
        return $this->goal;
    }

    // Setters
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setProfile(string $profile): self {
        $this->profile = $profile;
        return $this;
    }

    public function setInitialPrompt(string $initialPrompt): self {
        $this->initialPrompt = $initialPrompt;
        return $this;
    }

    public function setGoal(string $goal): self {
        $this->goal = $goal;
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
        $sql = "INSERT INTO assistants (name, profile, initial_prompt, goal) VALUES (?, ?, ?, ?)";
        $params = [$this->name, $this->profile, $this->initialPrompt, $this->goal];
        
        if ($this->db->execute($sql, $params)) {
            $this->id = (int)$this->db->lastInsertId();
            return true;
        }
        return false;
    }

    private function update(): bool {
        $sql = "UPDATE assistants SET name = ?, profile = ?, initial_prompt = ?, goal = ? WHERE id = ?";
        $params = [$this->name, $this->profile, $this->initialPrompt, $this->goal, $this->id];
        
        return $this->db->execute($sql, $params);
    }

    public function delete(): bool {
        if (!$this->id) {
            return false;
        }

        // Verificar se há conversas associadas
        $conversationCount = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM conversations WHERE assistant1_id = ? OR assistant2_id = ?",
            [$this->id, $this->id]
        );

        if ($conversationCount['count'] > 0) {
            throw new Exception("Não é possível excluir o assistente pois há conversas associadas.");
        }

        $sql = "DELETE FROM assistants WHERE id = ?";
        return $this->db->execute($sql, [$this->id]);
    }

    // Métodos estáticos para busca
    public static function findById(int $id, Database $database = null): ?self {
        $db = $database ?: new Database();
        $data = $db->fetchOne("SELECT * FROM assistants WHERE id = ?", [$id]);
        
        if (!$data) {
            return null;
        }

        $assistant = new self($db);
        $assistant->loadFromArray($data);
        return $assistant;
    }

    public static function findAll(Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll("SELECT * FROM assistants ORDER BY name");
        
        $assistants = [];
        foreach ($results as $data) {
            $assistant = new self($db);
            $assistant->loadFromArray($data);
            $assistants[] = $assistant;
        }
        
        return $assistants;
    }

    public static function findByName(string $name, Database $database = null): array {
        $db = $database ?: new Database();
        $results = $db->fetchAll(
            "SELECT * FROM assistants WHERE name LIKE ? ORDER BY name",
            ['%' . $name . '%']
        );
        
        $assistants = [];
        foreach ($results as $data) {
            $assistant = new self($db);
            $assistant->loadFromArray($data);
            $assistants[] = $assistant;
        }
        
        return $assistants;
    }

    public static function count(Database $database = null): int {
        $db = $database ?: new Database();
        $result = $db->fetchOne("SELECT COUNT(*) as count FROM assistants");
        return (int)$result['count'];
    }

    // Métodos auxiliares
    private function loadFromArray(array $data): void {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->profile = $data['profile'];
        $this->initialPrompt = $data['initial_prompt'];
        $this->goal = $data['goal'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'profile' => $this->profile,
            'initial_prompt' => $this->initialPrompt,
            'goal' => $this->goal
        ];
    }

    // Validação
    public function validate(): array {
        $errors = [];

        if (empty($this->name)) {
            $errors['name'] = 'O nome é obrigatório.';
        } elseif (strlen($this->name) > 255) {
            $errors['name'] = 'O nome deve ter no máximo 255 caracteres.';
        }

        if (empty($this->profile)) {
            $errors['profile'] = 'O perfil é obrigatório.';
        }

        if (empty($this->initialPrompt)) {
            $errors['initial_prompt'] = 'O prompt inicial é obrigatório.';
        }

        if (empty($this->goal)) {
            $errors['goal'] = 'O objetivo é obrigatório.';
        }

        return $errors;
    }

    public function isValid(): bool {
        return empty($this->validate());
    }

    // Métodos para estatísticas
    public function getConversationCount(): int {
        if (!$this->id) {
            return 0;
        }

        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM conversations WHERE assistant1_id = ? OR assistant2_id = ?",
            [$this->id, $this->id]
        );
        
        return (int)$result['count'];
    }

    public function getLastConversationDate(): ?string {
        if (!$this->id) {
            return null;
        }

        $result = $this->db->fetchOne(
            "SELECT MAX(start_time) as last_conversation FROM conversations WHERE assistant1_id = ? OR assistant2_id = ?",
            [$this->id, $this->id]
        );
        
        return $result['last_conversation'];
    }
}

