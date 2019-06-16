<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Comments;
use App\Repository\ArticlesRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Identicon\Identicon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class CommentForHexoController extends AbstractController
{

    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');
    }

    /**
     * @Route("uuid", name="getUuid")
     * @param RequestStack $requestStack
     * @param ArticlesRepository $articlesRepository
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUuid(RequestStack $requestStack, ArticlesRepository $articlesRepository, EntityManagerInterface $entityManager)
    {
        $identity = $requestStack->getMasterRequest()->get('identity');
        $identity = urldecode($identity);
        $article  = $articlesRepository->findOneBy(['identity' => $identity]);
        if ($article) {

        } else {
            $article = new Articles();
            $article->setIdentity($identity);
            $article->setUuid(uniqid(rand(), true));
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
        $nickname = strip_tags($requestStack->getMasterRequest()->get('nickname'));
        $email    = strip_tags($requestStack->getMasterRequest()->get('email', ''));
        $website  = strip_tags($requestStack->getMasterRequest()->get('website', ''));
        $uuid     = $requestStack->getMasterRequest()->get('uuid');
        $content  = strip_tags($requestStack->getMasterRequest()->get('content'));


        if (!$uuid || !$content || !$nickname) {

        }

        $article = $articlesRepository->findOneBy(['uuid' => $uuid]);

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

        $identity = $requestStack->getMasterRequest()->get('identity');
        $identity = urldecode($identity);
        $article  = $articlesRepository->findOneBy(['identity' => $identity]);
        if ($article) {

        } else {
            $article = new Articles();
            $article->setIdentity($identity);
            $article->setUuid(uniqid(rand(), true));
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());
            $entityManager->persist($article);
            $entityManager->flush();
        }

//        $uuid    = $requestStack->getMasterRequest()->get('uuid');
//        $article = $articlesRepository->findOneBy(['uuid' => $uuid]);

        $commentsResult = [];
        if ($article) {
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
        }

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
