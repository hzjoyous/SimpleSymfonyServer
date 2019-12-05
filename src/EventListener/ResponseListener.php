<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseListener
{
    public function doListen(ResponseEvent $event)
    {
        $response     = $event->getResponse();
        $response->headers->add([
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
