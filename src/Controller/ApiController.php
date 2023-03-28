<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ApiController extends AbstractController
{
    #[Route(
        path: '/users/me',
        name: 'user_get',
        defaults: [
            '_api_resource_class' => User::class,
        ],
    )]
    public function __invoke()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->json($this->getUser());
    }
}