<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Session;
use App\Entity\User;
use App\Exception\BusinessException;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use App\Service\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Parser as JWTParer;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    use Response;

    const JWT_REFRESH_TTL = '1209600‬';


    /**
     * @Route("/register", name="注册")
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param JwtService $jwtService
     * @return JsonResponse
     * @throws BusinessException
     */
    public function register(RequestStack $requestStack, EntityManagerInterface $entityManager, JwtService $jwtService): JsonResponse
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr = json_decode($requestContent, true);
        $username = $requestArr['username'] ?? '';
        $password = $requestArr['password'] ?? '';

        if (!$username || !$password) {
            throw new BusinessException("输入不合法");
        }
        $user = new User();
        $user->setUsername($username);
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $entityManager->persist($user);
        $entityManager->flush();

        $token = $jwtService->generatorJwt($user->getId());

        return $this->responseData([
            'accessToken' => (string)$token,
            'userInfo' => [
                'userId' => $user->getId(),

            ]
        ]);
    }


    /**
     * @Route("/login", name="登陆",methods={"POST"})
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws BusinessException
     */
    public function login(RequestStack $requestStack, EntityManagerInterface $entityManager, UserRepository $userRepository, JwtService $jwtService): JsonResponse
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr = json_decode($requestContent, true);
        $username = $requestArr['username'] ?? '';
        $password = $requestArr['password'] ?? '';


        $user = $userRepository->findOneBy([
            'username' => $username
        ]);

        if ($user === null || !password_verify($password, $user->getPassword())) {
            throw new BusinessException('登陆信息输入有误');
        }

        $token = $jwtService->generatorJwt($user->getId());

        return $this->responseData([
            'accessToken' => (string)$token,
            'userInfo' => [
                'username' => $user->getUsername()
            ]
        ]);
    }


    /**
     * @Route("/refreshToken",name="refreshToken")
     * @param JwtService $jwtService
     * @return mixed
     */
    public function refreshToken(JwtService $jwtService)
    {
        $token = $jwtService->refreshToken();

        return $this->responseData([
            "accessToken" => (string)$token
        ]);
    }
}
