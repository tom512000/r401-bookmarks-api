<?php

namespace App\Factory;

use App\Entity\User;
use App\Repository\UserRepository;
use Jdenticon\Identicon;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<User>
 *
 * @method        User|Proxy                     create(array|callable $attributes = [])
 * @method static User|Proxy                     createOne(array $attributes = [])
 * @method static User|Proxy                     find(object|array|mixed $criteria)
 * @method static User|Proxy                     findOrCreate(array $attributes)
 * @method static User|Proxy                     first(string $sortedField = 'id')
 * @method static User|Proxy                     last(string $sortedField = 'id')
 * @method static User|Proxy                     random(array $attributes = [])
 * @method static User|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method static User[]|Proxy[]                 all()
 * @method static User[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static User[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static User[]|Proxy[]                 findBy(array $attributes)
 * @method static User[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static User[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    private UserPasswordHasherInterface $passwordHasher;
    private \Transliterator $transliterator;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();

        $this->passwordHasher = $passwordHasher;
        $this->transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        $firstname = $this->normalizeName(self::faker()->firstName());
        $lastname = $this->normalizeName(self::faker()->lastName());
        $avatar = self::createAvatar($this->transliterator->transliterate($lastname.$firstname));

        return [
            'login' => self::faker()->unique()->numerify('user###'),
            'roles' => [],
            'password' => 'test',
            'firstname' => $firstname,
            'lastname' => $lastname,
            'avatar' => $avatar,
            'email' => mb_strtolower($firstname).'.'.mb_strtolower($lastname).'@'.self::faker()->domainName(),
        ];
    }

    protected static function createAvatar(string $value)
    {
        $icon = new Identicon([
            'size' => 50,
            'value' => $value,
        ]);

        return fopen($icon->getImageDataUri('png'), 'r');
    }

    protected function normalizeName(string $text): string
    {
        return preg_replace('/[^A-Za-z]+/', '-', $this->transliterator->transliterate($text));
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this->afterInstantiate(function (User $user) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        });
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
