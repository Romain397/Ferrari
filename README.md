# Ferrari

Application web Symfony autour de l'univers Ferrari: actualités, sport automobile, boutique, panier et commandes.

## Aperçu

Le projet propose une expérience complète:
- page d'accueil avec des voitures emblématiques
- page sport auto dédiée aux courses
- store avec filtres et panier dynamique
- gestion des commandes utilisateur
- back-office admin (articles, produits, utilisateurs)

## Fonctionnalités

### Côté visiteur / utilisateur
- Inscription et connexion
- Consultation des articles Ferrari (Accueil + Sport Auto)
- Consultation du store
- Ajout au panier, modification des quantités, suppression d'articles
- Passage de commande
- Suivi des commandes et statuts

### Côté administrateur
- Gestion des articles (CRUD)
- Gestion des produits store (CRUD)
- Gestion des utilisateurs (rôles + suppression)

### Gestion des médias
- Upload d'images en local (posts/produits)
- Support d'image via URL web
- Validation serveur des fichiers image

## Stack technique

- PHP 8.4+
- Symfony 8
- Twig
- Doctrine ORM + Migrations
- Symfony Security, Forms, Validator
- Bootstrap 5 + CSS personnalisé
- JavaScript vanilla (panier)

## Installation

### 1) Cloner le projet
```bash
git clone <url-du-repo>
cd Ferrari
```

### 2) Installer les dépendances
```bash
composer install
```

### 3) Configurer l'environnement
Créer un fichier `.env.local` avec vos valeurs (minimum `APP_SECRET` et `DATABASE_URL`).

Exemple:
```env
APP_ENV=dev
APP_SECRET=change_me
DATABASE_URL="mysql://user:password@127.0.0.1:3306/ferrari?serverVersion=8.0"
APP_TIMEZONE=Europe/Paris
```

### 4) Créer la base et exécuter les migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5) Lancer le serveur
```bash
symfony server:start
```
ou
```bash
php -S 127.0.0.1:8000 -t public
```

## Comptes et rôles

- `ROLE_USER`: accès aux fonctionnalités utilisateur (commandes)
- `ROLE_ADMIN`: accès aux routes `/admin` (gestion globale)

## Structure du projet

```text
src/
  Controller/
  Entity/
  Form/
  Repository/
  Twig/
templates/
public/
  img/
  uploads/
config/
migrations/
```

## Qualité et vérifications utiles

```bash
php -l src/Controller/PostController.php
php -l src/Controller/ProductController.php
php bin/console lint:twig templates
```

## Captures / démo

Ajoutez ici quelques captures d'écran du projet:
- Accueil
- Sport Auto
- Store
- Dashboard Admin

## Roadmap (améliorations possibles)

- Ajouter des tests automatisés (fonctionnels + unitaires)
- Renforcer encore la sécurité des actions sensibles (CSRF partout)
- Optimiser davantage les images et le cache HTTP
- Ajouter pagination et recherche avancée

## Auteur

Projet réalisé dans le cadre d'un projet de formation Symfony.

---
Si ce projet vous aide, vous pouvez laisser une étoile sur le repo.
