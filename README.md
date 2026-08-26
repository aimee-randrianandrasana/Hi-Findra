```
▄▀▄
   █████╗  ██╗ ███╗   ███╗ ███████╗ ███████╗
  ██╔══██╗ ██║ ████╗ ████║ ██╔════╝ ██╔════╝
  ███████║ ██║ ██╔████╔██║ █████╗   █████╗  
  ██╔══██║ ██║ ██║╚██╔╝██║ ██╔════╝ ██╔════╝
  ██║  ██║ ██║ ██║ ╚═╝ ██║ ███████╗ ███████╗
  ╚═╝  ╚═╝ ╚═╝ ╚═╝     ╚═╝ ╚══════╝ ╚══════╝
```

# Mi-Findra - Gestion des Affectations

Application web de gestion des affectations geographiques des employes au sein d'une organisation, developpee avec un framework MVC personnalise en PHP.

## Fonctionnalites

- CRUD complet des affectations (ajout, modification, suppression)
- Gestion des employes et des lieux d'affectation
- Generation de PDF pour les arretes d'affectation (DomPDF)
- Envoi de notifications par email (PHPMailer / SMTP Gmail)
- Protection contre les attaques par force brute
- Architecture MVC personnalisee avec autoloading PSR-4
- Protection CSRF sur tous les formulaires
- Reinitialisation de mot de passe par email
- Systeme "Se souvenir de moi" avec pattern selecteur/validateur
- Roles utilisateurs (admin, gestionnaire)
- Pagination et recherche sur toutes les entites

## Technologies

- **Backend** : PHP 8.1+, Framework MVC personnalise
- **Base de donnees** : MariaDB
- **PDF** : DomPDF
- **Email** : PHPMailer
- **Dependances** : Composer

## Installation

```bash
# Cloner le projet
git clone <url-du-depot>
cd gestion-affectations

# Installer les dependances
composer install

# Configurer l'environnement
cp .env.example .env
# Modifier .env avec vos identifiants MariaDB

# Importer le schema de la base de donnees
mysql -u root -p gestion_affectations < database/schema.sql

# Lancer le serveur PHP integre
php -S localhost:8000
```

## Compte par defaut

- **Login** : `joker@gmail.com`
- **Mot de passe** : `joker@test`

## Structure du projet

```
gestion-affectations/
├── app/
│   ├── Controllers/     # Gestionnaires de requetes
│   ├── Core/            # Framework (Router, Database, Controller, Model, etc.)
│   ├── Helpers/         # Fonctions globales, Validator
│   ├── Middleware/       # Auth, Guest middleware
│   ├── Models/          # Acces aux donnees (PDO)
│   └── Views/           # Templates PHP avec layouts
├── config/              # Configuration et routes
├── database/            # Schema SQL
├── public/              # Racine web (index.php, CSS, JS, uploads)
├── storage/logs/        # Journaux d'application
└── composer.json
```

## Securite

- Mots de passes hashes avec `PASSWORD_DEFAULT` (bcrypt)
- Tokens CSRF sur chaque formulaires
- Protection contre les attaques par force brute (tentatives configurables + verrouillage)
- Expiration de session par inactivite.
- Cookies de session securises (httponly, samesite=Lax)
- Validation du type MIME sur les uploads.

## Auteur

**Randrianandrasana Jean Aime**
- GitHub : [aimee-randrianandrasana](https://github.com/aimee-randrianandrasana)
