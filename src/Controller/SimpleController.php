<?php

namespace App\Controller;

use App\Service\JwtService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class SimpleController extends AbstractController
{

    public function __construct(JwtService $jwtService)
    {
        $jwtService->verifyJwt();
    }

    /**
     * @Route("/simple", name="simple")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SimpleController.php',
        ]);
    }
}
