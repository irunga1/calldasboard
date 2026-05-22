<?php
session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL',  rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

require_once BASE_PATH . '/config/config.php';

spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . "/system/core/$class.php",
        BASE_PATH . "/app/controllers/$class.php",
        BASE_PATH . "/app/models/$class.php",
        BASE_PATH . "/app/libs/$class.php",
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) { require $file; return; }
    }
});

// ── URL helper ────────────────────────────────────────────────────────────────
// Genera: /calldashboard/index.php/controller/method
// Sin necesidad de mod_rewrite. Apache sirve index.php y el resto es PATH_INFO.
function route(string $controller, string $method = 'index', array $params = []): string {
    $url = BASE_URL . '/index.php/' . strtolower($controller);
    if ($method !== 'index') $url .= '/' . $method;
    if ($params) $url .= '?' . http_build_query($params);
    return $url;
}

// ── Routing via PATH_INFO ─────────────────────────────────────────────────────
// URL: /calldashboard/index.php/auth/login → PATH_INFO = /auth/login
// Fallback a query string (?c=auth&m=login) para compatibilidad.
$pathInfo = trim($_SERVER['PATH_INFO'] ?? '', '/');

if ($pathInfo !== '') {
    $parts          = explode('/', $pathInfo);
    $controllerName = ucfirst(preg_replace('/[^a-zA-Z0-9_]/', '', $parts[0])) . 'Controller';
    $methodName     = preg_replace('/[^a-zA-Z0-9_]/', '', $parts[1] ?? 'index') ?: 'index';
} else {
    // Fallback query-string (mantiene compatibilidad con links antiguos)
    $controllerName = ucfirst(preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['c'] ?? 'auth')) . 'Controller';
    $methodName     = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['m'] ?? 'index') ?: 'index';
}

// ── Auth middleware ───────────────────────────────────────────────────────────
$publicControllers = ['AuthController'];
if (!in_array($controllerName, $publicControllers) && empty($_SESSION['user_id'])) {
    if ($controllerName === 'ApiController') {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    header('Location: ' . route('auth', 'login'));
    exit;
}

// ── Dispatch ──────────────────────────────────────────────────────────────────
if (!class_exists($controllerName)) {
    http_response_code(404);
    echo '<h3>404 — Controlador no encontrado: ' . htmlspecialchars($controllerName) . '</h3>';
    exit;
}

$controller = new $controllerName();

if (!method_exists($controller, $methodName)) {
    http_response_code(404);
    echo '<h3>404 — Método no encontrado: ' . htmlspecialchars($methodName) . '</h3>';
    exit;
}

$controller->$methodName();
