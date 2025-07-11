<?php

abstract class Controller {
    protected $db;
    protected $errors = [];
    protected $success = [];
    protected $data = [];

    public function __construct() {
        $this->db = new Database();
    }

    // Métodos para gerenciar mensagens de feedback
    protected function addError(string $message): void {
        $this->errors[] = $message;
        $_SESSION['errors'] = $this->errors;
    }

    protected function addSuccess(string $message): void {
        $this->success[] = $message;
        $_SESSION['success'] = $this->success;
    }

    protected function getErrors(): array {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        return $errors;
    }

    protected function getSuccess(): array {
        $success = $_SESSION['success'] ?? [];
        unset($_SESSION['success']);
        return $success;
    }

    protected function hasErrors(): bool {
        return !empty($_SESSION['errors']);
    }

    protected function hasSuccess(): bool {
        return !empty($_SESSION['success']);
    }

    // Métodos para validação de entrada
    protected function validateRequired(array $fields, array $data): array {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "O campo {$field} é obrigatório.";
            }
        }
        return $errors;
    }

    protected function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function validateLength(string $value, int $min = 0, int $max = null): bool {
        $length = strlen($value);
        if ($length < $min) {
            return false;
        }
        if ($max !== null && $length > $max) {
            return false;
        }
        return true;
    }

    // Métodos para sanitização
    protected function sanitizeString(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    protected function sanitizeInt(mixed $value): int {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    protected function sanitizeFloat(mixed $value): float {
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    // Métodos para redirecionamento
    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    protected function redirectWithError(string $url, string $message): void {
        $this->addError($message);
        $this->redirect($url);
    }

    protected function redirectWithSuccess(string $url, string $message): void {
        $this->addSuccess($message);
        $this->redirect($url);
    }

    // Métodos para renderização de views
    protected function render(string $view, array $data = []): void {
        // Adicionar dados globais
        $data['errors'] = $this->getErrors();
        $data['success'] = $this->getSuccess();
        $data['base_url'] = BASE_URL;
        $data['site_name'] = SITE_NAME;

        // Extrair variáveis para o escopo da view
        extract($data);

        // Incluir o template
        $viewFile = __DIR__ . "/../painel/views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View não encontrada: {$view}");
        }
    }

    protected function renderLayout(string $view, array $data = []): void {
        // Adicionar dados globais
        $data['errors'] = $this->getErrors();
        $data['success'] = $this->getSuccess();
        $data['base_url'] = BASE_URL;
        $data['site_name'] = SITE_NAME;
        $data['content_view'] = $view;

        // Extrair variáveis para o escopo da view
        extract($data);

        // Incluir o layout principal
        $layoutFile = __DIR__ . "/../painel/layout.php";
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            throw new Exception("Layout não encontrado: layout.php");
        }
    }

    // Métodos para JSON response (para AJAX)
    protected function jsonResponse(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function jsonSuccess(string $message = 'Sucesso', array $data = []): void {
        $this->jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function jsonError(string $message = 'Erro', array $errors = [], int $statusCode = 400): void {
        $this->jsonResponse([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    // Métodos para paginação
    protected function paginate(int $total, int $perPage = 20, int $currentPage = 1): array {
        $totalPages = ceil($total / $perPage);
        $currentPage = max(1, min($currentPage, $totalPages));
        $offset = ($currentPage - 1) * $perPage;

        return [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'offset' => $offset,
            'has_prev' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'prev_page' => $currentPage > 1 ? $currentPage - 1 : null,
            'next_page' => $currentPage < $totalPages ? $currentPage + 1 : null
        ];
    }

    // Métodos para verificação de método HTTP
    protected function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function isPut(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'PUT';
    }

    protected function isDelete(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'DELETE';
    }

    protected function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // Métodos para obter dados da requisição
    protected function getPostData(): array {
        return $_POST;
    }

    protected function getGetData(): array {
        return $_GET;
    }

    protected function getRequestData(): array {
        return array_merge($_GET, $_POST);
    }

    protected function getJsonData(): array {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?: [];
    }

    // Método para verificar CSRF (se implementado)
    protected function verifyCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    protected function generateCsrfToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Métodos utilitários
    protected function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
        return date($format, strtotime($date));
    }

    protected function formatBytes(int $bytes, int $precision = 2): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    protected function generateSlug(string $text): string {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    // Método abstrato que deve ser implementado pelas classes filhas
    abstract public function index(): void;
}

