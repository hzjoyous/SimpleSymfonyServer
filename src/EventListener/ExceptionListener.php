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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception     = $event->getException();
        $exceptionType = get_class($exception);
        $errorMsg      = '';
        if ($exception instanceof NotFoundHttpException) {
            $errorMsg = 'NotFound';
        }

        $response = new JsonResponse([
            'code'    => 500,
            'data'   => null,
            'errMsg'  => $errorMsg ? $errorMsg : $exception->getMessage(),
            'timeStamp' => time(),
        ]);

        //$response->headers->replace($exception->getHeaders());
        //$response->setStatusCode($exception->getStatusCode());
        switch ($exceptionType) {
            case HttpExceptionInterface::class:
                /**
                 * @var HttpExceptionInterface $exception
                 */
                $response->setStatusCode($exception->getCode());
                break;
            case BusinessException::class:
                $response->setStatusCode(Response::HTTP_OK);
                break;
            case NotFoundHttpException::class:
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                break;
            default:
                break;
        }

        $event->setResponse($response);
    }
}
