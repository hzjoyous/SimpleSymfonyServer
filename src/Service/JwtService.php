<?php


namespace App\Service;

use App\Entity\Session;
use App\Exception\BusinessException;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\ValidationData;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Lcobucci\JWT\Token;

class JwtService
{
    const JWT_REFRESH_TTL = 1209600;

    /**
     * @var Request|null
     */
    private $request;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SessionRepository
     */
    private $sessionRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RequestStack $request, UserRepository $userRepository, SessionRepository $sessionRepository, EntityManagerInterface $entityManager)
    {
        $this->request = $request->getMasterRequest();
        $this->userRepository = $userRepository;
        $this->sessionRepository = $sessionRepository;
        $this->entityManager = $entityManager;
    }

    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }
        return false;
    }

    protected function parseAuthorizationHeader()
    {
        return trim(str_ireplace($this->getAuthorizationMethod(), '', $this->request->headers->get('authorization')));
    }

    public function validateAuthorizationHeader()
    {
        if (self::startsWith(strtolower($this->request->headers->get('authorization')), 'bearer')) {
            return true;
        }
        throw new BadRequestHttpException;
    }

    protected function getAuthorizationMethod()
    {
        return 'bearer';
    }

    public function getToken()
    {
//        $this->validateAuthorizationHeader();
//        $token = $this->parseAuthorizationHeader();
        $content = $this->request->getContent();
        $request = json_decode($content, true);
        $token = $request['accessToken'] ?? '';
        if(!$token){
            throw new HttpException(401, '令牌丢失');
        }
        $token = (new Parser())->parse((string)$token);
        return $token;
    }

    /**
     * 1 验签是否有效，
     * 2 是否时间有效
     * 3 会话是否可以维持
     * @param bool $isRefresh
     * @return Token|string
     */
    public function verifyJwt($isRefresh = false)
    {
        $token = $this->getToken();
        $publicKey = new Key('file://' . __DIR__ . '/../../resource/pub_key.pem');
        $signer = new Sha256();
        if (!$token->verify($signer, $publicKey)) {
            throw new HttpException(401, '401');
        }

        if (!$isRefresh) {
            if (time() > ($token->getClaim('iat') + self::JWT_REFRESH_TTL)) {
                throw new HttpException(401, '令牌过期2');
            }
        } else {
            if (time() > $token->getClaim('exp')) {
                throw new HttpException(401, '令牌过期1');
            }
        }

        $sessionId = $token->getClaim('sessionId');
        $session = $this->sessionRepository->findBy(['id' => $sessionId, 'status' => 1]);
        if (!$session) {
            throw new HttpException(401, '会话过期');
        }
        return $token;
    }

    public function refreshToken()
    {
        $token = $this->verifyJwt(true);
        return $this->generatorJwt(null, $token->getClaim('sessionId'));
    }

    /**
     * @param $userId
     * @param null $sessionId
     * @return string
     */
    public function generatorJwt($userId = null, $sessionId = null)
    {
        $origin = $this->request->headers->get('clientFrom', 'web');
        if (!in_array($origin, ['web'])) {
            throw new HttpException(404, 'not found');
        }
        $signer = new Sha256();

        $privateKey = new Key('file://' . __DIR__ . '/../../resource/pri_key.pem');
        $time = time();
        if (!$sessionId) {
            $sessionList = $this->sessionRepository->findBy(['userId' => $userId, 'clientFrom' => $origin]);
            /**
             * 将同源session 失效
             */
            foreach ($sessionList as $oldSession) {
                $oldSession->setStatus(0);
                $this->entityManager->persist($oldSession);
            }
            $session = new Session();
            $session->setExp(time() + 86400 * 365)->setUserId($userId)->setClientFrom($origin);
            $this->entityManager->persist($session);
            $this->entityManager->flush();
            $sessionId = $session->getId();
        }

        $token = (new Builder())->issuedBy('simple') // Configures the issuer (iss claim)
        ->permittedFor('*')                      // Configures the audience (aud claim)
        //->identifiedBy('4f1g23a12aa', true)              // Configures the id (jti claim), replicating as a header item
        ->issuedAt($time)                                  // Configures the time that the token was issue (iat claim)
        ->canOnlyBeUsedAfter($time)                        // Configures the time that the token can be used (nbf claim)
        ->expiresAt($time + 3600)               // Configures the expiration time of the token (exp claim)
        ->withClaim('sessionId', $sessionId) // Configures a new claim, called "uid"
        ->getToken($signer, $privateKey);                  // Retrieves the generated token
        return (string)$token;
    }

}