# TP1 - Introduction à API Platform

## Auteur :
- Tom SIKORA (siko0001)

## Script :
- `composer start` : Lance le serveur web de test.
- `composer test:cs` : Lance la commande de vérification du code par PHP CS Fixer.
- `composer fix:cs` : Lance la commande de correction du code par PHP CS Fixer.
- `composer test` : Teste la mise en forme du code.
- `composer db` : Détruit et recrée la base de données, migre sa structure et regénère les données factices.

<br>

## TP1 - Introduction à API Platform
### 1. Mise en place d'API Platform
- `composer self-update` : Mise à jour de Composer.
- Mise à jour de Symfony.
- `symfony new r401-bookmarks-api --version 6.3` : Création du projet.
- `composer require api api-platform/core:^3.2` : Installation des composants PHP d'API Platform (sans inclure les fichiers de configuration de Docker).

<br>

### 2. Gestion du dépôt Git
- `git add .` : Suivi de tous les fichiers du répertoire par Git.
- `git commit -m "require api dependencies"` : Réalisation du second « commit » du dépôt.
- Création du dépôt distant.
- `git remote add origin https://iut-info.univ-reims.fr/gitlab/siko0001/r401-bookmarks-api.git` : Association du dépôt local au dépôt distant.
- Ajout de l'intervenant de TP au dépôt distant avec les droits de « reporter ».
- Ajout de « .idea » dans le fichier « .gitignore ».

<br>

### 3. Serveur de développement
- `symfony serve` : Démarrage du serveur local.
- Accès à l'application Symfony (http://127.0.0.1:8000).
- Accès à l'API (http://127.0.0.1:8000/api).
- Création d'un nouveau script « start » dans le fichier « composer.json » en levant la restriction de durée d'exécution.
- Ajout de la description de la commande dans le fichier « composer.json ».
- `composer run --list` : Listage des commandes (scripts).
- Documentation du projet dans le fichier « README.md ».

<br>

### 4. Style de codage
- `composer require --dev friendsofphp/php-cs-fixer` : Ajout de PHP CS Fixer comme dépendance du projet.
- Configuration de PHP CS Fixer sur PhpStorm (PHP > Quality Tool > PHP CS Fixer).
- Ajout de 3 scripts Composer :
  - « test:cs » qui déclenche la commande `php-cs-fixer fix --dry-run`.
  - « fix:cs » qui déclenche la commande `php-cs-fixer fix`.
  - « test » qui déclenche le script « test:cs » avec la commande `@test:cs`.

<br>

### 5. Connexion de la base de données
- Création d'une nouvelle base de données MySQL (siko0001_Bookmarks).
- Copie du fichier « .env » en « .env.local » en ajoutant la ligne `DATABASE_URL="mysql://identifiant:mot-de-passe@mysql:3306/nom-de-la-base-de-donnée?serverVersion=mariadb-10.2.25"`.

<br>

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

<br>

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
- `composer db` : Réinitialisation de la base de données.
- `bin/console dbal:run-sql "SELECT COUNT(*) FROM bookmark"` : Vérification du fonctionnement du script « bd ».
- Obtention du message :
  ```
   ---------- 
    COUNT(*)  
   ---------- 
    20        
   ----------
  ```

<br>

### 8. Des données plus réalistes
- Ajout d'un jeu de données plus réaliste à partir du fichier « bookmark.json ».
- Modification de la méthode « load » :
  ```php
  ...
  $file = __DIR__.'/data/bookmarks.json';
  $data = json_decode(file_get_contents($file), true);
  foreach ($data as $tab) {
        BookmarkFactory::createOne($tab);
  }
  ...
  ```
- `composer db` : Réinitialisation de la base de données.
- `bin/console dbal:run-sql "SELECT COUNT(*) FROM bookmark"` : Vérification du fonctionnement du script « bd ».
- Obtention du message :
  ```
   ---------- 
    COUNT(*)  
   ---------- 
    67        
   ----------
  ```

<br>

### 9. Configuration des opérations API Platform
- Création d'un nouveau « bookmark » sur la page de l'API (POST /api/bookmarks).
- `bin/console dbal:run-sql "SELECT COUNT(*) FROM bookmark"` : Listage du nombre de « bookmarks ».
- Obtention du message :
  ```
   ---------- 
    COUNT(*)  
   ---------- 
    68        
   ----------
  ```
- Modification du « bookmark » sur la page de l'API (PATCH /api/bookmarks/{id}).
- Suppression du « bookmark » sur la page de l'API (DELETE /api/bookmarks/{id}).
- `bin/console dbal:run-sql "SELECT COUNT(*) FROM bookmark"` : Listage du nombre de « bookmarks ».
- Obtention du message :
  ```
   ---------- 
    COUNT(*)  
   ---------- 
    67        
   ----------
  ```
- Listage des « bookmarks » sur la page de l'API (GET /api/bookmarks).
- Ajout du tris par ordre alphabétique des noms des « bookmarks » dans le fichier « Bookmark.php » :
  ```php
  ...
  #[ApiResource(order: ['name' => 'ASC'])]
  ...
  ```
- Ajout d'une option de choix de tris (par date de création ou par nom) pour l'utilisateur dans le fichier « Bookmark.php » :
  ```php
  ...
  #[ApiFilter(OrderFilter::class, properties: ['creationDate', 'name'], arguments: ['orderParameterName' => 'order'])]
  ...
  ```
- Ajout d'un filtre permettant de ne sélectionner que les « bookmarks » public dans le fichier « Bookmark.php » :
  ```php
  ...
  #[ApiFilter(BooleanFilter::class, properties: ['isPublic'])]
  ...
  ```
- Ajout d'un attribut permettant de rechercher des « bookmarks » par une partie de leur nom ou de leur description dans le fichier « Bookmark.php » :
  ```php
  ...
  #[ApiFilter(SearchFilter::class, properties: ['name' => 'partial', 'description' => 'partial'])]
  ...
  ```

<br>

### 10. Mise en place de tests
- Installation de la suite de tests de Codeception ainsi que des modules utiles pour la suite du projet.
- `php vendor/bin/codecept bootstrap --namespace=App\\Tests --empty` : Initialisation de Codeception.
- Ajout de la ligne `DATABASE_URL="sqlite:///%kernel.project_dir%/var/test.db"` dans le fichier « .env.test ».
- Configuration de Codeception pour utiliser les fichiers « .env » et « .env.test » comme sources de paramètres dans « codeception.yml ».
- `php vendor/bin/codecept generate:suite Api` : Génération de la première suite de tests « Api ».
- Exclusion du répertoire tests/Support/_generated dans le fichier « .php-cs-fixer.dist.php ».
- Ajout d'un nouveau script « test:codeception » afin de créer la base de données de test et y ajouter les tables.
- Ajout d'un appel à ce script Composer dans le script « test », après l'appel au script « test:cs ».
- Ajout du module « ApiPlatform » dans le répertoire tests/Support/Helper.
- Ajout du module « EntityManagerReset » dans le répertoire tests/Support/Helper.
- Modification de la propriété modules du fichier « Api.suite.yml ».
- Ajout de la classe de Cests « BookmarkGetCest.php » dans le répertoire tests/Api/Bookmark.
- `composer test` : Exécution des tests de mise en forme et Codeception, test valide avec toutes ses assertions.
- `composer require --dev --no-interaction symfony/proxy-manager-bridge` : Ajout d'une nouvelle dépendance Composer pour supprimer les notifications de déprecation.
- Ajout d'un nouveau jeu de tests « BookmarkGetCollectionCest.php » dans le répertoire tests/Api/Bookmark/.
- `composer test` : Vérification de la validité des tests (OK).

<br>

## TP2 - Gestion des utilisateurs
### 1. Création de la table User
- `bin/console make:user` : Création de l'entité « User », stockée dans la base de données et utilisant la propriété d'identification « login » pour l'authentification.
- `bin/console make:entity User` : Génération des propiétés suivantes pour l'entité « User ».
  - Nom : firstname, type : string, taille : 30 et ne pouvant pas être null.
  - Nom : lastname, type : string, taille : 40 et ne pouvant pas être null.
  - Nom : avatar, type : blob et ne pouvant pas être null.
  - Nom : email, type : string, taille : 100 et ne pouvant pas être null.
- `bin/console doctrine:schema:update --complete --dump-sql` : Contrôle de la validité de la commande SQL produite.
- `bin/console make:migration` : Mise à jour de la base de données à travers l'utilisation d'une migration.
- `bin/console doctrine:migrations:migrate` : Exécution de la migration.

<br>

### 2. Génération d'utilisateurs
- `bin/console make:factory` : Création d'une nouvelle forge « UserFactory ».
- `composer require jdenticon/jdenticon` : Installation du paquet Composer de la bibliothèque « Jdenticon » afin de générer l'avatar de chaque utilisateur.
- Ajout de la méthode « createAvatar » dans « UserFactory » afin de générer un avatar de 50 pixels au format PNG à partir du paramètre « value ».
- Modification de la méthode afin de générer les propriétés suivantes :
  - login : La chaîne de caractères 'user' suivie d'un nombre unique sur 3 caractères.
  - roles : Un tableau vide.
  - password : La chaîne de caractères 'test'.
  - firstname : Un faux prénom.
  - lastname : Un faux nom.
  - avatar : Le résultat de la méthode createAvatar avec en paramètre une combinaison du nom et du prénom.
- Ajout de la méthode « normalizeName » afin de translittérer et standardisé le nom et le prénom.
- Modification de la méthode « initialize » pour hacher le mot de passe après l'initialisation d'un utilisateur.
- `bin/console make:fixtures` : Création d'une nouvelle classe de génération de contenu pour la table « UserFixtures ».
- Création de 3 utilisateurs 'user1', 'user2', 'user3' et de 20 utilisateurs aléatoires dans « UserFixtures ».
- `composer db` : Remplissage de la base de données.
- `bin/console dbal:run-sql "SELECT login, firstname, lastname, email FROM user LIMIT 5"` : Vérification des fixtures.
- Modification de la localisation de Faker dans « config/packages/zenstruck_foundry.yaml » :
  ```yaml
  ...
  faker:
      locale: fr_FR
  ...
  ```
- `bin/console dbal:run-sql "SELECT login, firstname, lastname, email FROM user LIMIT 5"` : Vérification des fixtures en français.
- `bin/console dbal:run-sql "SELECT id, password FROM user"` : Vérification de la génération des mots de passe.

<br>

### 3. Mise en place de l'authentification
- `bin/console make:auth` : Mise en place de l'authentification.
- Création d'un authentificateur reposant sur un « Login form authenticator » nommé « LoginFormAuthenticator ». Le contrôleur se nommera « SecurityController » et utilisera une route pour que les utilisateurs se déconnectent. L'application pourra également se souvenir des utilisateurs en cochant la case « Rester connecté » à la connexion.
- Gestion de l'option « Rester connecté » :
  - Mise en commentaire de l'option « always_remember_me » du fichier « config/packages/security.yaml ». 
  - Ajout d'une case à cocher dans le formulaire d'authentification.
- Création de la feuille de style « public/css/login.css ».
- Insertion de cette feuille dans le twig « security/login.html.twig ».
- Mise à jour du texte du bouton de soumission pour les tests.
- Vérification du fonctionnement de la page de connexion (http://127.0.0.1:8000/login).
- Modification de la méthode « onAuthenticationSuccess » dans la classe « App\Security\LoginFormAuthenticator » afin de gérer la redirection.
  ```php
  ...
  return new RedirectResponse($this->urlGenerator->generate('api_doc'));
  ...
  ```
- Vérification du fonctionnement de la redirection (http://127.0.0.1:8000/login).
