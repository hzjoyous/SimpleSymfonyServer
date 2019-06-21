<?php
/**
 * Created by PhpStorm.
 * User: hzj
 * Date: 2019/3/4
 * Time: 19:15
 */
declare(strict_types=1);

namespace App\EventListener;

use App\Exception\BusinessException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception     = $event->getException();
        $exceptionType = get_class($exception);

        $response = new JsonResponse([
            'status'    => 4000,
            'content'   => null,
            'errorMsg'  => $exception->getMessage(),
            'timeStamp' => time(),
        ]);

        switch ($exceptionType) {
            case HttpExceptionInterface::class:
                /**
                 * @var HttpExceptionInterface $exception
                 */
//                $response->headers->replace($exception->getHeaders());
//                $response->setStatusCode($exception->getStatusCode());
                break;
            case BusinessException::class:
                $response->setStatusCode(Response::HTTP_OK);
                break;
            default:
                break;
        }

        $event->setResponse($response);
    }
}