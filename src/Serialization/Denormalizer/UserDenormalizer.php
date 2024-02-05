<?php

namespace App\Serialization\Denormalizer;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UserDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public const ALREADY_CALLED = 'USER_DENORMALIZER_ALREADY_CALLED';

    private UserPasswordHasherInterface $passwordHasher;
    private Security $security;

    public function __construct(UserPasswordHasherInterface $passwordHasher, Security $security)
    {
        $this->passwordHasher = $passwordHasher;
        $this->security = $security;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) && User::class === $type;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        $user = $this->security->getUser();
        if (isset($data['password'])) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $data['password'] = $hashedPassword;
        }

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }
}
