<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DouFuGangController extends AbstractController
{
    /**
     * @Route("", name="dou_fu_gang_i")
     */
    public function i()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DouFuGangController.php',
        ]);
    }

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

    /**
     * @Route("/opclean", name="opclean")
     */
    public function opclean()
    {
        if(function_exists('opcache_reset')){
            $result = opcache_reset();
        } else {
            $result = false;
        }
        if ($result) {
            $result = ('opclean success');
        } else {
            $result = ('no use opcache');
        }
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }
    /**
     * @Route("/showPhpnfo", name="showPhpnfo")
     */
    public function showPhpnfo()
    {
        phpinfo();
    }
}
