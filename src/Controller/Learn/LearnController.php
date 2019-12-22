<?php

namespace App\Controller\Learn;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LearnController extends AbstractController
{
    /**
     * @Route("/learn", name="learn")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/LearnController.php',
        ]);
    }

    /**
     * @Route("/wordList/{pageId}",name="wordlist")
     */
    public function getWordList($pageId)
    {
        $wordList = file_get_contents(__DIR__ . '/english' . $pageId . '.json');
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => [
                'wordlist'=>json_decode($wordList,true)
            ]
        ]);
    }
}
