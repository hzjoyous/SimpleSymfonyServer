<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');
    }

    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    /**
     * @Route("/test/list", name="test_list")
     */
    public function list()
    {
        $i = 10;
        $data = [];
        while ($i--) {
            $data[] = [
                'text' => '内容' . (10 - $i),
                'imgSrc' => 'images/php.png'
            ];
        }
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => $data,
        ]);
    }

    /**
     * @Route("/test/DBTest", name="test_DBTest")
     * @param UserRepository $fitnessStatisticsRepository
     */
    public function apiDBTest(UserRepository $userRepository)
    { 
        $user = $userRepository->findOneBy([
            
        ]);
        
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => [
                'userId'=>$user->getId()
            ],
        ]);
    }
}
