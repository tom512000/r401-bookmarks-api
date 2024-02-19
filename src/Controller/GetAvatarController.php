<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GetAvatarController extends AbstractController
{
    public function __invoke(User $user): Response
    {
        return new Response(
            stream_get_contents($user->getAvatar(), -1, 0),
            Response::HTTP_OK,
            ['content-type' => 'image/png']
        );
    }
}
