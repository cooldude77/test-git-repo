<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    #[Route('/api/v1/', name: 'api_v1_home')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        return $this->json([
            'message' => 'Welcome to the api'
        ]);
    }


}
