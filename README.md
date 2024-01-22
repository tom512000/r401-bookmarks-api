# TP1 - Introduction à API Platform

## Auteur :
- Tom SIKORA (siko0001)

## Script :
- `composer start` : Lance le serveur web de test.
- `composer test:cs` : Lance la commande de vérification du code par PHP CS Fixer.
- `composer fix:cs` : Lance la commande de correction du code par PHP CS Fixer.
- `composer test` : Teste la mise en forme du code.
- `composer db` : Détruit et recrée la base de données, migre sa structure et regénère les données factices.

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

### 5. Connexion de la base de données
- Création d'une nouvelle base de données MySQL (siko0001_Bookmarks).
- Copie du fichier « .env » en « .env.local » en ajoutant la ligne `DATABASE_URL="mysql://identifiant:mot-de-passe@mysql:3306/nom-de-la-base-de-donnée?serverVersion=mariadb-10.2.25"`.

### 6. Création d'une table pour les bookmarks
- `composer require --dev symfony/maker-bundle` : Installation du paquet « PHP maker-bundle » avec Composer.
- `bin/console make:entity` : Création d'une nouvelle entité « Bookmark » avec les propiétés suivantes.
  - Nom : name, type : string, taille : 255 et ne pouvant pas être null.
  - Nom : description, type : text et ne pouvant pas être null.
  - Nom : creationDate, type : datetime et ne pouvant pas être null.
  - Nom : isPublic, type : boolean et ne pouvant pas être null.
  - Nom : url, type : text et ne pouvant pas être null.
- `bin/console doctrine:schema:update --complete --dump-sql` : Vérification de la validité des instructions SQL.
- `bin/console make:migration` : Création de la migration de la base de données.
- `bin/console doctrine:migrations:migrate` : Application de la nouvelle migration.
- `bin/console dbal:run-sql "SELECT * FROM bookmark"` : Réalisation d'une requête sur la table pour vérifier la bonne création de la table.
- Obtention du message `[OK] The query yielded an empty result set.`.

### 7. Génération de données
- `composer require orm-fixtures --dev` : Installation de l'outil de gestion des « fixtures ».
- `bin/console make:fixtures` : Génération d'un nouveau script de génération de contenu pour la table.
- `composer require zenstruck/foundry --dev` : Installation de l'outil « Foundry ».
- `bin/console make:factory` : Création d'une nouvelle forge de données pour l'entité Bookmark.
- Utilisation de Faker pour initialiser les attributs :
  - name avec un faux nom de société.
  - description avec un paragraphe de texte généré.
  - creationDate avec une date entre maintenant et il y a deux ans.
  - isPublic avec un booléen.
  - url avec une fausse url.
- Remplacement du contenu de la méthode « load » de « BookmarkFixtures » pour générer le jeu de données dans la base de données :
  ```php
  <?php
  
  namespace App\DataFixtures;
  
  use App\Factory\BookmarkFactory;
  use Doctrine\Bundle\FixturesBundle\Fixture;
  use Doctrine\Persistence\ObjectManager;
  
  class BookmarkFixtures extends Fixture
  {
      public function load(ObjectManager $manager)
      {
          BookmarkFactory::createMany(20);
      }
  }
  ```
- Création du script « bd » afin de recréer la base de données avec les commandes :
  - `php bin/console doctrine:database:drop --force` : Supprime la base de données.
  - `php bin/console doctrine:database:create` : Crée la base de données.
  - `php bin/console doctrine:migrations:migrate --no-interaction` : Applique les migrations à la base de données.
  - `php bin/console doctrine:fixtures:load --no-interaction` : Charge les données de test dans la base de données.
- `bin/console dbal:run-sql "SELECT COUNT(*) FROM bookmark"` : Vérification du fonctionnement du script « bd ».
- Obtention du message :
  ```
   ---------- 
    COUNT(*)  
   ---------- 
    20        
   ----------
  ```
