<?php
/**
 * Created by PhpStorm.
 * User: hzj
 * Date: 2019/3/4
 * Time: 19:15
 */
declare(strict_types=1);

namespace App\EventListener;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class TerminateEventListener
{

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function onKernelTerminate(TerminateEvent $event): void
    {
        $response   = $event->getResponse();
        $request    = $event->getRequest();
        $requestUri = $request->server->get('REQUEST_URI');
        $httpHost   = $request->server->get('HTTP_HOST');
        $httpQuery  = $request->query;

        $this->logger->emergency('RequestAndResponse', [
                'url'             => $httpHost . $requestUri,
                'httpQuery'       => json_encode($httpQuery),
                'requestContent'  => $request->getContent(),
                'responseContent' => $response->getContent(),
            ]
        );
    }

}