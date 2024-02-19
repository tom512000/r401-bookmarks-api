<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Controller\GetAvatarController;
use App\Repository\UserRepository;
use App\State\MeProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/me',
            openapiContext: [
                'summary' => 'Retrieves the connected user',
                'description' => 'Retrieves the connected user',
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
            ],
            normalizationContext: [
                'groups' => ['User_me', 'User_read']],
            security: "is_granted('ROLE_USER')",
            provider: MeProvider::class
        ),
        new Get(
            normalizationContext: ['groups' => ['User_read']]
        ),
        new Patch(
            normalizationContext: ['groups' => ['User_me', 'User_read']],
            denormalizationContext: ['groups' => ['User_write']],
            security: "is_granted('ROLE_USER') and object == user"
        ),
        new Get(
            uriTemplate: '/users/{id}/avatar',
            formats: [
                'png' => 'image/png',
            ],
            controller: GetAvatarController::class,
            openapiContext: [
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
            ],
        ),
    ],
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups('User_read')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['User_read', 'User_write'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $login = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[Groups('User_write')]
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['User_read', 'User_write'])]
    #[ORM\Column(length: 30)]
    private ?string $firstname = null;

    #[Groups(['User_read', 'User_write'])]
    #[ORM\Column(length: 40)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::BLOB)]
    private $avatar;

    #[Groups(['User_write', 'User_me'])]
    #[ORM\Column(length: 100)]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
