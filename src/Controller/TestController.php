<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\Test1925Event;
use App\Events\TestEvent;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    /**
     * @Route("/test/list", name="test_list")
     */
    public function list(EventDispatcherInterface $eventDispatcher): Response
    {
        $i = 10;
        $data = [];
        while ($i--) {
            $data[] = [
                'text' => '内容' . (10 - $i),
                'imgSrc' => 'images/php.png'
            ];
        }
        
        $eventDispatcher->dispatch(new TestEvent('asdasdsd'));
        $eventDispatcher->dispatch(new Test1925Event('asdasdsd'));
        
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => $data,
        ]);
    }

    /**
     * @Route("/test/DBTest", name="test_DBTest")
     * @param UserRepository $userRepository
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     */
    public function apiDBTest(UserRepository $userRepository, EventDispatcherInterface $eventDispatcher): Response
    {
        $userList = $userRepository->findAll();
        $userInfo = array_map(function(User $user){
            return ['userName'=>$user->getUsername()];
        },$userList);
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => [
                'userInfo' => $userInfo
            ],
        ]);
    }
}
