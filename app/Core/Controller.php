<?php

declare(strict_types=1);

namespace App\Core;

// Controleur de base - rendu de vues factorise pour tous les controleurs
abstract class Controller
{
    // Affiche une vue, eventuellement enveloppee dans un layout
    protected function view(string $view, array $data = [], string $layout = 'layouts/app'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new \RuntimeException("Vue introuvable : {$view}");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = dirname(__DIR__) . '/Views/' . $layout . '.php';

        if (is_file($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    // Renvoie une reponse JSON
    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Redirige vers une autre URL interne
    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }
}
