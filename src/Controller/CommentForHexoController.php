<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Repository\ArticlesRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Identicon\Identicon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class CommentForHexoController extends AbstractController
{
    /**
     * @Route("uuid", name="getUuid")
     * @param RequestStack $requestStack
     * @param ArticlesRepository $articlesRepository
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUuid(RequestStack $requestStack, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager)
    {
        $request = $requestStack->getMasterRequest();
        if (!($request instanceof Request)) {
            throw new \Exception('');
        }
        $identity = $request->get('identity');
        $identity = urldecode($identity);
        $article  = $articlesRepository->findOneBy(['identity' => $identity]);
        if ($article) { } else {
            $article = new Articles();
            $article->setIdentity($identity);
            $article->setUuid(uniqid((string) rand(), true));
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());
            $entityManager->persist($article);
            $entityManager->flush();
        }

        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => $article->getUuid(),
        ]);
    }

    /**
     * @Route("addComment", name="添加评论")
     * @param RequestStack $requestStack
     * @param ArticlesRepository $articlesRepository
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addCommentByUUID(RequestStack $requestStack, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager)
    {
        $request = $requestStack->getMasterRequest();
        if (!($request instanceof Request)) {
            throw new \Exception('');
        }
        $nickname = strip_tags($request->get('nickname'));
        $email    = strip_tags($request->get('email', ''));
        $website  = strip_tags($request->get('website', ''));
        $uuid     = $request->get('uuid');
        $content  = strip_tags($request->get('content'));


        if (!$uuid || !$content || !$nickname) {
            throw new \Exception('参数不合法');
        }

        $article = $articlesRepository->findOneBy(['uuid' => $uuid]);
        if (!$article) {
            throw new \Exception('article 获取失败');
        }

        $avatarUri = (new Identicon())->getImageDataUri($nickname . $email);

        $comment = new Comments();
        $comment->setContent($content);
        $comment->setEmail($email);
        $comment->setAvatarUri($avatarUri);
        $comment->setNickname($nickname);
        $comment->setWebsite($website);
        $comment->setArticleId($article->getId());
        $comment->setUpdatedAt(new \DateTime());
        $comment->setCreatedAt(new \DateTime());

        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => '',
        ]);
    }

    /**
     * @Route("comments", name="获取评论")
     * @param RequestStack $requestStack
     * @param ArticlesRepository $articlesRepository
     * @param CommentsRepository $commentsRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getComment(RequestStack $requestStack, ArticlesRepository $articlesRepository, CommentsRepository $commentsRepository, EntityManagerInterface $entityManager)
    {
        $request = $requestStack->getMasterRequest();
        if (!($request instanceof Request)) {
            throw new \Exception('');
        }
        $identity = $request->get('identity');
        $identity = urldecode($identity);
        $article  = $articlesRepository->findOneBy(['identity' => $identity]);
        if ($article) { } else {
            $article = new Articles();
            $article->setIdentity($identity);
            $article->setUuid(uniqid((string) rand(), true));
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());
            $entityManager->persist($article);
            $entityManager->flush();
        }

        //        $uuid    = $requestStack->getMasterRequest()->get('uuid');
        //        $article = $articlesRepository->findOneBy(['uuid' => $uuid]);

        $comments       = $commentsRepository->findBy(['articleId' => $article->getId()]);
        $commentsResult = array_map(function (Comments $item) {
            return [
                'nickname'   => $item->getNickname(),
                'email'      => $item->getEmail(),
                'avatarUri'  => $item->getAvatarUri(),
                'content'    => $item->getContent(),
                'createTime' => $item->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }, $comments);


        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => [
                'comments' => $commentsResult,
                'uuid'     => $article->getUuid()
            ],
        ]);
    }
}
