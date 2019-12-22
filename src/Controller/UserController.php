<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Exception\BusinessException;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Parser as JWTParer;
use Lcobucci\JWT\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path'    => 'src/Controller/UserController.php',
        ]);
    }

    /**
     * @Route("/login", name="登陆")
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws BusinessException
     */
    public function login(RequestStack $requestStack, EntityManagerInterface $entityManager, UserRepository $userRepository):JsonResponse
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr     = json_decode($requestContent, true);
        $username       = $requestArr['username'] ?? '';
        $password       = $requestArr['password'] ?? '';

        $user = $userRepository->findOneBy([
            'username' => $username,
            'password' => $password,
        ]);
        if ($user === null) {
            throw new BusinessException('登陆信息输入有误');
        }
        $session = new Session();
        $session->setUserId($user->getId());
        $session->setExp((string)(time() + 3600));
        $session->setOrigin('api');
        $entityManager->persist($session);
        $entityManager->flush();

        $time  = (new \DateTime())->getTimestamp();
        $token = (new Builder())
            ->issuedBy('https://nonodi.com')
            ->issuedAt($time) // Configures the time that the token was issue (iat claim)
            ->canOnlyBeUsedAfter($time + 0) // Configures the time that the token can be used (nbf claim)
            ->expiresAt($time + 60) // Configures the expiration time of the token (exp claim)
            ->withClaim('username', $user->getUsername())
            ->withClaim('sessionId', $session->getId())
            ->getToken();

        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => [
                'token'    => (string) $token,
                'userInfo' => [
                    'username' => $user->getUsername()
                ]
            ],
        ]);
    }

    /**
     * @Route("/register", name="注册")
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws BusinessException
     */
    public function register(RequestStack $requestStack, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr     = json_decode($requestContent, true);
        $username       = $requestArr['username'] ?? '';
        $password       = $requestArr['password'] ?? '';

        if (!($username && $password)) {
            throw new BusinessException("输入不合法");
        }

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => [
                'userInfo' => [
                    'userId'=>$user->getId()
                ]
            ],
        ]);
    }

    /**
     * @Route("/jwtCheck", name="jwt校验")
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws BusinessException
     */
    public function jwtCheck(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr     = json_decode($requestContent, true);
        $tokenContent   = $requestArr['token'];
        $token          = (new JWTParer())->parse((string) $tokenContent);
        if ($token->isExpired()) {
            throw new BusinessException('认证已过期');
        }
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => 1
        ]);
    }

    /**
     * @Route("/sessionCheck", name="session验证")
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param SessionRepository $sessionRepository
     * @return JsonResponse
     * @throws BusinessException
     */
    public function sessionCheck(RequestStack $requestStack, EntityManagerInterface $entityManager, SessionRepository $sessionRepository)
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr     = json_decode($requestContent, true);
        $tokenContent   = $requestArr['token'];
        $token          = (new JWTParer())->parse((string) $tokenContent);
        $session        = $sessionRepository->findOneBy(['id' => $token->getClaim('sessionId')]);
        dd($session);
        if (!$session && $session->getStatus() !== 1 && $session->getExp() < time()) {
            throw new BusinessException('会话过期');
        }
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => 1
        ]);
    }
}
