<?php
// Configurações gerais do sistema
define("SITE_NAME", "Painel Administrativo - Sistema de Assistentes");
define("SITE_VERSION", "1.0.0");

// Determinar BASE_URL dinamicamente
$protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http";
$host = $_SERVER["HTTP_HOST"];

// Obtém o caminho do script atual no servidor (ex: /dial_man/painel/index.php)
$script_name = $_SERVER["SCRIPT_NAME"];

// Extrai o diretório base do script (ex: /dial_man/painel)
$base_dir = dirname($script_name);

// Garante que o BASE_URL termine sem barra, a menos que seja a raiz
$base_url_path = rtrim($base_dir, ".");
$base_url_path = rtrim($base_url_path, "/");

// Se o diretório base for a raiz do documento, defina como vazio para não ter barra dupla
if ($base_url_path === "." || $base_url_path === "/") {
    $base_url_path = "";
}

define("BASE_URL", $protocol . "://" . $host . $base_url_path);

define("TIMEZONE", "America/Sao_Paulo");

// Configurações de sessão
ini_set("session.cookie_httponly", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.cookie_secure", 0); // Mude para 1 em produção com HTTPS

// Definir timezone
date_default_timezone_set(TIMEZONE);

// Autoload das classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . "/../src/" . $class . ".php";
    if (file_exists($file)) {
        require_once $file;
    }
});

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para sanitizar e validar dados
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// CSRF Token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Variáveis globais para views
$site_name = SITE_NAME;
$base_url = BASE_URL;
$errors = $_SESSION["errors"] ?? [];
$success = $_SESSION["success"] ?? [];

unset($_SESSION["errors"]);
unset($_SESSION["success"]);


