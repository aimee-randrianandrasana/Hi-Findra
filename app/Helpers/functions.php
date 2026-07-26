<?php

declare(strict_types=1);

use App\Core\Database;

// Fonctions globales reutilisables

// Echappe une chaine pour un affichage HTML securise (anti-XSS)
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Genere (ou reutilise) le token CSRF de la session courante
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Champ cache pret a inserer dans un formulaire
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

// Verifie le token CSRF soumis
function csrf_verify(): void
{
    $submitted = $_POST['csrf_token'] ?? '';

    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submitted)) {
        http_response_code(419);
        exit('Session expiree, veuillez rafraichir la page et reessayer.');
    }
}

// Depose un message flash affiche une seule fois
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);

    return $value;
}

// Nom de l'application
function app_name(): string
{
    static $name = null;

    if ($name === null) {
        $name = (require dirname(__DIR__, 2) . '/config/config.php')['app']['name'];
    }

    return $name;
}

// Recupere l'utilisateur actuellement connecte
function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

// Verifie que l'utilisateur connecte possede l'un des roles donnes
function has_role(string ...$roles): bool
{
    $user = auth_user();
    return $user !== null && in_array($user['role'], $roles, true);
}

// Construit une URL Gravatar a partir d'un email
function gravatar_url(string $email, int $size = 200): string
{
    $hash = md5(strtolower(trim($email)));
    return "https://www.gravatar.com/avatar/{$hash}?d=mp&s={$size}";
}

// Construit une URL absolue depuis un chemin relatif
function url(string $path = ''): string
{
    static $base = null;

    if ($base === null) {
        $base = (require dirname(__DIR__, 2) . '/config/config.php')['app']['url'];
    }

    return $base . '/' . ltrim($path, '/');
}

// Renvoie l'instance PDO partagee
function db(): PDO
{
    return Database::getInstance();
}
