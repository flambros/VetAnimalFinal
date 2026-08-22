<?php
/**
 * Helpers de sesión / autenticación.
 * Usamos $_SESSION en vez de localStorage (más seguro en PHP).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Devuelve el usuario logueado (array asociativo) o null.
 */
function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function is_veterinario(): bool {
    return is_logged_in() && $_SESSION['user']['rol'] === 'veterinario';
}

/**
 * Corta la ejecución y redirige a /login.php si no hay sesión.
 * Usar en las páginas .php (no en los endpoints de /api/).
 */
function require_login_page(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_vet_page(): void {
    if (!is_logged_in() || !is_veterinario()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Para endpoints de /api/: corta con 401 JSON si no hay sesión.
 */
function require_login_api(): void {
    if (!is_logged_in()) {
        http_response_code(401);
        echo json_encode(['error' => 'Debés iniciar sesión.']);
        exit;
    }
}

function require_vet_api(): void {
    if (!is_logged_in() || !is_veterinario()) {
        http_response_code(403);
        echo json_encode(['error' => 'No tenés permisos de veterinario.']);
        exit;
    }
}
