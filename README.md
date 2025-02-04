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

<br>

### 4. Configuration des opérations API Platform
- Ajout de l'attribut PHP8 « #[ApiResource] » à la classe « User ».
- Ajout des 2 actions GET et PATCH à la classe « User ».
- Création de 2 groupes de sérialisation :
  - « User_read » pour la normalisation.
  - « User_write » pour la dénormalisation, pour les actions (PATCH).
- Ajout des accès aux attributs de lecture GET (id, login, firstname et lastname).
- Ajout des accès aux attributs de modification PATCH (login, password, firstname, lastname et email).
- `bin/console cache:clear` : Nettoyage le cache.
- Ajout des accès à PATCH pour un utilisateur authentifié et dont les modifications ne concernent que ses données.
- Modification de l'option stateless à false dans la configuration d'API Platform « config/packages/api_platform.yaml ».
- Surchargement de la méthode « start » de « LoginFormAuthenticator ».
- Création d'un nouveau groupe de sérialisation appellé « User_me » et ayant comme unique attribut « email » :
  ```php
  ...
  normalizationContext: ['groups' => ['User_me', 'User_read']],
  ...
  #[Groups(['User_write', 'User_me'])]
  private ?string $email = null;
  ...
  ```
- Ajout des 2 tests « UserGetCest.php » et « UserPatchCest.php » dans le répertoire tests/Api/User.
- Ajout d'une configuration spécifique à l'environnement de test dans le fichier « config/services.yaml » :
  ```yaml
  when@test:
    services:
        # Disable logger to avoid showing errors during tests
        Psr\Log\NullLogger: ~
        logger: '@Psr\Log\NullLogger'
  ```
- `APP_ENV=test bin/console cache:clear` : Effacement du cache pour l'environnement de test.
- `composer test` : Lancement des tests (OK).

<br>

### 5. Modification des données lors de la dénormalisation
- Création de la classe « UserDenormalizer.php » à l'aide de l'assistant PhpStorm avec :
  - Une constante « ALREADY_CALLED » avec comme valeur 'USER_DENORMALIZER_ALREADY_CALLED'.
  - Une propriété « passwordHasher » initialisée dans le constructeur avec le service « UserPasswordHasherInterface ».
  - Une propriété « security » initialisée dans le constructeur avec le service « Security ».
  - Une méthode « supportsDenormalization » qui retourne un booléen indiquant si la classe doit transformer les données.
  - Une méthode « denormalize() » qui doit réaliser la transformation avec les mêmes informations.
- Ajout du test « UserPatchPasswordCest.php » dans le répertoire tests/Api/User.
- `composer test` : Lancement des tests (OK).

<br>

### 6. Ajout d'une route personnalisée vers l'utilisateur connecté
- `bin/console make:state-provider MeProvider` : Génération d'une nouvelle source de données appelée « MeProvider ».
- Ajout d'une instance de Symfony\Bundle\SecurityBundle\Security dans le constructeur afin d'initialiser la propriété « $security » :
  ```php
  ...
  private Security $security;

  public function __construct(Security $security)
  {
      $this->security = $security;
  }
  ...
  ```
- Utilisation de la méthode « getUser() » de Security pour obtenir l'utilisateur courant dans la méthode provide :
  ```php
  ...
  public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
  {
      return $this->security->getUser() ?? null;
  }
  ...
  ```
- Ajout d'une nouvelle action personnalisée dans l'entité « User » :
  ```php
  new Get(
      uriTemplate: '/me',
      openapiContext: [
          'summary' => 'Retrieves the connected user',
          'description' => 'Retrieves the connected user',
      ],
      provider: MeProvider::class,
  ),
  ```
- Ajout d'une action afin que l'utilisateur ne puisse accéder à une ressource que s'il est authentifié :
  ```php
  ...
  security: "is_granted('ROLE_USER')",
  ...
  ```
- Utilisation du groupe de sérialisation « User_me » pour identifier les attributs accessibles à l'utilisateur connecté.
- Ajout du test « UserGetMeCest.php » dans le répertoire tests/Api/User.
- `composer test` : Lancement des tests (1 erreur : 401 - Authentication).
- Modification de l'accès aux ressources à l'aide de la propriété « access_control » de la configuration globale de la sécurité dans le fichier « security.yaml » :
  ```yaml
  access_control:
      - {path: ^/api$, roles: PUBLIC_ACCESS}
      - {path: ^/api/bookmarks, roles: PUBLIC_ACCESS}
      - {path: ^/api/users/\d+$, roles: PUBLIC_ACCESS}
      - {path: ^/api/*, roles: IS_AUTHENTICATED_FULLY}
  ```
- `composer test` : Lancement des tests (OK).
- Redéfinissage du contenu de la réponse HTTP 200 en faisant référence au schéma généré à l'aide des groupes de sérialisation :
  ```php
  'responses' => [
     '200' => [
        'description' => 'connected user resource',
        'content' => [
            'application/ld+json' => [
                'schema' => [
                    '$ref' => '#/components/schemas/User.jsonld-User_me_User_read',
                ],
            ],
        ],
     ],
  ],
  ```

<br>

### 7. Ajout d'une route personnalisée vers l'avatar d'un utilisateur
- Ajout du test « UserGetAvatarCest.php » dans le répertoire tests/Api/User.
- `bin/console make:controller --no-template GetAvatarController` : Génération d'un nouveau contrôleur appelé « GetAvatarController », sans template twig associé.
- Création d'une méthode d'instance publique __invoke qui :
  - Retourne une instance de « Symfony\Component\HttpFoundation\Response ».
  - Modifie l'en-tête Content-Type de cette réponse pour qu'il soit égal à image/png.
  - Possède le contenu de la réponse avec l'avatar de l'utilisateur reçu en paramètre.
- Création d'une opération Get dans le tableau operations de l'attribut « #[ApiResource] » de l'entité User associée à la ressource « /users/{id}/avatar » et au contrôleur « GetAvatarController » :
  ```php
  new Get(
    uriTemplate: '/users/{id}/avatar',
    formats: [
        'png' => 'image/png',
    ],
    controller: GetAvatarController::class,
  ),
  ```
- Ajout d'une nouvelle règle d'accès à la ressource « /users/{id}/avatar » dans le fichier « security.yaml » :
  ```yaml
  access_control:
      - {path: ^/api/users/\d+/avatar$, roles: PUBLIC_ACCESS}
  ```
- Surcharge de la documentation OpenAPI de l'opération en définissant le paramètre openapiContext :
  ```php
  'responses' => [
      '200' => [
          'description' => 'The user avatar',
          'content' => [
              'image/png' => [
                  'schema' => [
                      'type' => 'string',
                      'format' => 'binary',
                  ],
              ],
          ],
      ],
      '404' => [
          'description' => 'User does not exist',
      ],
  ],
  ```

<br>

### 8. Validation des données
- `php vendor/bin/codecept generate:cest Api User\\UserPatchDataValidationCest` : Création de notre premier jeu de tests permettant de valider le contrôle des données.
- Création de la méthode de classe « expectedProperties » qui retourne un tableau associatif avec comme clés les attributs de normalisation de PATCH et leur type comme valeur associée :
  ```php
  protected static function expectedProperties(): array
  {
      return [
          'id' => 'integer',
          'login' => 'string',
          'firstname' => 'string',
          'lastname' => 'string',
          'email' => 'string:email',
      ];
  }
  ```
- Création de la méthode de test « loginUnicityTest » qui réalise le test de validation de l'unicité du login :
  ```php
  public function loginUnicityTest(ApiTester $I): void
  {
      // 1. 'Arrange'
      $dataAuth = [
          'login' => 'authenticated',
          'password' => 'password',
      ];
      /** @var $userAuth User */
      $userAuth = UserFactory::createOne()->object();
      UserFactory::createOne($dataAuth);
      $I->amLoggedInAs($userAuth);

      $dataLog = [
          'login' => 'login',
          'password' => 'password',
      ];
      /** @var $userLog User */
      $userLog = UserFactory::createOne()->object();
      UserFactory::createOne($dataLog);

      // 2. 'Act'
      $dataPatch = [
          'login' => 'login',
      ];
      $I->sendPatch('/api/users/1', $dataPatch);

      // 3. 'Assert'
      $I->seeResponseCodeIs(422);
  }
  ```
- Ajout d'une contrainte d'unicité sur le login des instances de User à l'aide de la contraintes « UniqueEntity » :
  ```php
  #[UniqueEntity('login')]
  ```
- Ajout de la methode de test de vérification avec son fournisseur de données associé dans la classe « UserPatchDataValidationCest ».
- Ajout d'un contrôle sur la propriété email :
  ```php
  #[Assert\Email(
      message: 'The email {{ value }} is not a valid email.',
  )]
  ```
- Interdiction à l'aide d'une expression rationnelle des caractères « < », « > », « & » et « " » pour les propriétés login, firstname et lastname :
  ```php
  #[Assert\Regex(
      pattern: '/^[^<>&"]*$/',
      message: 'Le login ne peut pas contenir les caractères "<", ">", "&" et ""."'
  )]
  protected ?string $login = null;
  ...
  #[Assert\Regex(
      pattern: '/^[^<>&"]*$/',
      message: 'Le prénom ne peut pas contenir les caractères "<", ">", "&" et ""."'
  )]
  protected ?string $firstname = null;
  ...
  #[Assert\Regex(
      pattern: '/^[^<>&"]*$/',
      message: 'Le nom de famille ne peut pas contenir les caractères "<", ">", "&" et ""."'
  )]
  protected ?string $lastname = null;
  ```
- Surcharge des exemples en utilisant le paramètre « example » de l'attribut PHP « #[ApiProperty] » des propriétés login, firstname et lastname dans l'entité « User » :
  ```php
  #[ApiProperty(example: 'user4')]
  protected ?string $login = null;
  ...
  #[ApiProperty(example: 'Tom')]
  protected ?string $firstname = null;
  ...
  #[ApiProperty(example: 'Sikora')]
  protected ?string $lastname = null;
  ```
