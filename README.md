# TP1 - Introduction à API Platform

## Auteur :
- Tom SIKORA (siko0001)

### 1. Mise en place d'API Platform
- `composer self-update` : Mise à jour de Composer.
- Mise à jour de Symfony.
- `symfony new r401-bookmarks-api --version 6.3` : Création du projet.
- `composer require api api-platform/core:^3.2` : Installation des composants PHP d'API Platform (sans inclure les fichiers de configuration de Docker).

### 2. Gestion du dépôt Git
- `git add .` : Suivi de tous les fichiers du répertoire par Git.
- `git commit -m "require api dependencies"` : Réalisation du second « commit » du dépôt.
- Création du dépôt distant.
- `git remote add origin https://iut-info.univ-reims.fr/gitlab/siko0001/r401-bookmarks-api.git` : Association du dépôt local au dépôt distant.
- Ajout de l'intervenant de TP au dépôt distant avec les droits de « reporter ».
- Ajout de « .idea » dans le fichier « .gitignore ».

### 3. Serveur de développement
- `symfony serve` : Démarrage du serveur local.
- Accès à l'application Symfony (http://127.0.0.1:8000).
- Accès à l'API (http://127.0.0.1:8000/api).
- Création d'un nouveau script « start » dans le fichier « composer.json » en levant la restriction de durée d'exécution.
- Ajout de la description de la commande dans le fichier « composer.json ».
- `composer run --list` : Listage des commandes (scripts).
- Documentation du projet dans le fichier « README.md ».

### 4. Style de codage
- `composer require --dev friendsofphp/php-cs-fixer` : Ajout de PHP CS Fixer comme dépendance du projet.
- Configuration de PHP CS Fixer sur PhpStorm (PHP > Quality Tool > PHP CS Fixer).
- Ajout de 3 scripts Composer :
  - « test:cs » qui déclenche la commande `php-cs-fixer fix --dry-run`.
  - « fix:cs » qui déclenche la commande `php-cs-fixer fix`.
  - « test » qui déclenche le script « test:cs » avec la commande `@test:cs`.