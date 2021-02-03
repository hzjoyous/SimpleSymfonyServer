<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ToolController extends AbstractController
{
    /**
     * @Route("/tool", name="tool")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'This is ToolController',
        ]);
    }

    /**
     * @Route("/opclean", name="opclean")
     */
    public function opclean()
    {
        if (function_exists('opcache_reset')) {
            $result = opcache_reset();
        } else {
            $result = false;
        }
        if ($result) {
            $result = ('opcache reset success');
        } else {
            $result = ('no use opcache');
        }
        return $this->json([
            'status'  => 0,
            'message' => $result
        ]);
    }

    /**
     * @Route("/showRequest", name="showPhpinfo")
     */
    public function showRequest()
    {
        return $this->json([
            'get'=>$_GET,
            'post'=>$_POST
        ]);
    }
    /**
     * @Route("/showPhpinfo", name="showPhpinfo")
     */
    public function showPhpinfo()
    {
        phpinfo();
    }
}
