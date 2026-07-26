<?php

declare(strict_types=1);

namespace App\Helpers;

// Validateur simple et chainable pour les donnees de formulaire
final class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function required(string $field, string $label): self
    {
        if (trim((string) ($this->data[$field] ?? '')) === '') {
            $this->errors[$field] ??= "{$label} est obligatoire.";
        }

        return $this;
    }

    public function email(string $field): self
    {
        $value = $this->data[$field] ?? '';

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[$field] ??= "Le format de l'email est invalide.";
        }

        return $this;
    }

    public function minLength(string $field, int $min, string $label): self
    {
        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && mb_strlen($value) < $min) {
            $this->errors[$field] ??= "{$label} doit contenir au moins {$min} caracteres.";
        }

        return $this;
    }

    // Le mot de passe doit contenir au moins une lettre et un chiffre
    public function password(string $field): self
    {
        $value = (string) ($this->data[$field] ?? '');

        if ($value !== '' && !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $value)) {
            $this->errors[$field] ??= 'Le mot de passe doit contenir au moins 8 caracteres, une lettre et un chiffre.';
        }

        return $this;
    }

    public function matches(string $field, string $otherField, string $label): self
    {
        if (($this->data[$field] ?? null) !== ($this->data[$otherField] ?? null)) {
            $this->errors[$field] ??= "{$label} ne correspond pas.";
        }

        return $this;
    }

    public function in(string $field, array $allowed, string $label): self
    {
        $value = $this->data[$field] ?? null;

        if ($value !== null && $value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] ??= "{$label} est invalide.";
        }

        return $this;
    }

    // Ajoute manuellement une erreur (ex: unicite en base)
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] ??= $message;

        return $this;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function first(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
