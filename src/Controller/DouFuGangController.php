<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DouFuGangController extends AbstractController
{
    /**
     * @Route("/dou/fu/gang", name="dou_fu_gang")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DouFuGangController.php',
        ]);
    }
}
