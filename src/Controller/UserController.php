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
    use Response;

    const JWT_REFRESH_TTL = '1209600‬';


    /**
     * @Route("/register", name="注册",methods={"POST"})
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws BusinessException
     */
    public function register(RequestStack $requestStack, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr = json_decode($requestContent, true);
        $username = $requestArr['username'] ?? '';
        $password = $requestArr['password'] ?? '';

        if (!($username && $password)) {
            throw new BusinessException("输入不合法");
        }
        $user = new User();
        $user->setUsername($username);
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
        $entityManager->persist($user);
        $entityManager->flush();

        $session = new Session();
        $session->setUserId($user->getId());
        $session->setExp((string)(time() + 3600));
        $session->setOrigin('api');
        $entityManager->persist($session);
        $entityManager->flush();

        $time = time();
        $token = (new Builder())->issuedBy('simple') // Configures the issuer (iss claim)
        ->permittedFor('*') // Configures the audience (aud claim)
        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
        ->withClaim('sessionId', $session->getId()) // Configures a new claim, called "uid"
        ->getToken(); // Retrieves the generated token


        return $this->responseData([
            'userInfo' => [
                'userId' => $user->getId(),
                'password' => $user->getPassword(),
                'accessToken' => (string)$token
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
    public function login(RequestStack $requestStack, EntityManagerInterface $entityManager, UserRepository $userRepository): JsonResponse
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

        $session = new Session();
        $session->setUserId($user->getId());
        $session->setExp((string)(time() + 3600));
        $session->setOrigin('api');
        $entityManager->persist($session);
        $entityManager->flush();

        $time = time();
        $token = (new Builder())->issuedBy('simple') // Configures the issuer (iss claim)
        ->permittedFor('*') // Configures the audience (aud claim)
        ->identifiedBy('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
        ->issuedAt($time) // Configures the time that the token was issue (iat claim)
        ->canOnlyBeUsedAfter($time + 60) // Configures the time that the token can be used (nbf claim)
        ->expiresAt($time + 3600) // Configures the expiration time of the token (exp claim)
        ->withClaim('sessionId', $session->getId()) // Configures a new claim, called "uid"
        ->getToken(); // Retrieves the generated token


        return $this->responseData([
            'token' => (string)$token,
            'userInfo' => [
                'username' => $user->getUsername()
            ]
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
        $requestArr = json_decode($requestContent, true);
        $tokenContent = $requestArr['token'];
        $token = (new JWTParer())->parse((string)$tokenContent);
        if ($token->isExpired()) {
            throw new BusinessException('认证已过期');
        }
        return $this->responseData([1]);
    }

    /**
     * @Route("/sessionCheck", name="session验证",methods={"POST"})
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param SessionRepository $sessionRepository
     * @return JsonResponse
     * @throws BusinessException
     */
    public function sessionCheck(RequestStack $requestStack, EntityManagerInterface $entityManager, SessionRepository $sessionRepository)
    {
        $requestContent = $requestStack->getMasterRequest()->getContent();
        $requestArr = json_decode($requestContent, true);
        $tokenContent = $requestArr['token'];
        $token = (new JWTParer())->parse((string)$tokenContent);
        $session = $sessionRepository->findOneBy(['id' => $token->getClaim('sessionId')]);
        if (!$session && $session->getStatus() !== 1 && $session->getExp() < time()) {
            throw new BusinessException('会话过期');
        }
        return $this->responseData([1]);
    }

    /**
     * @Route("/refreshToken",name="refreshToken")
     */
    public function refreshToken()
    {
        return $this->responseData([
            "acce"
        ]);
    }
}
