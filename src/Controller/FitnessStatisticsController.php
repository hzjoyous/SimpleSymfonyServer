<?php

namespace App\Controller;

use App\Entity\FitnessStatistics;
use App\Exception\BusinessException;
use App\Repository\FitnessStatisticsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class FitnessStatisticsController extends AbstractController
{
    const userToken       = [
        'ajdlajsldjaskldjaklsjdklasjdal'   => [
            'userId' => 1
        ],
        'dsadjajskldjaklsdjalksdjaksdalda' => [
            'userId' => 2
        ],
    ];
    const trainingProgram = [
        'ShoulderPush' => ['id' => 1, 'name' => '肩推'],
        'AfterBundle'  => ['id' => 2, 'name' => '后束'],
        'FrontBundle'  => ['id' => 3, 'name' => '前束'],
        'Squat'        => ['id' => 4, 'name' => '深蹲'],
        'boating'      => ['id' => 5, 'name' => '划船'],
        'TwoCurls'     => ['id' => 6, 'name' => '二头弯举'],
    ];

    const trainingProgramId = [
        1 => ['type' => 'ShoulderPush', 'id' => 1, 'name' => '肩推'],
        2 => ['type' => 'AfterBundle', 'id' => 2, 'name' => '后束'],
        3 => ['type' => 'FrontBundle', 'id' => 3, 'name' => '前束'],
        4 => ['type' => 'Squat', 'id' => 4, 'name' => '深蹲'],
        5 => ['type' => 'boating', 'id' => 5, 'name' => '划船'],
        6 => ['type' => 'TwoCurls', 'id' => 6, 'name' => '二头弯举'],
    ];

    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');
    }

    /**
     * @Route("/fitness/statistics", name="fitness_statistics")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path'    => 'src/Controller/FitnessStatisticsController.php',
        ]);
    }

    /**
     * @Route("/addFitnessStatistics", name="addFitnessStatistics")
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param FitnessStatisticsRepository $fitnessStatisticsRepository
     */
    public function addFitnessStatistics(RequestStack $requestStack, EntityManagerInterface $entityManager, FitnessStatisticsRepository $fitnessStatisticsRepository)
    {

        $request       = $requestStack->getMasterRequest();
        $userToken     = $request->get('userToken');
        $todayItemList = $request->get('todayItemList');

        if (!($userId = self::userToken[$userToken]['userId'] ?? 0)) {
            throw new BusinessException("账号不存在");
        }
        foreach ($todayItemList as $item) {
            if (is_string($item)) {
                $item = json_decode($item, true);
            }
            $entity = $fitnessStatisticsRepository->findOneBy([
                'userId'         => $userId,
                'fitnessDate'    => (new \DateTime())->format('Y-m-d'),
                'fitnessProgram' => self::trainingProgram[$item['type']]['id']
            ]);
            if (!$entity) {
                $entity = new FitnessStatistics();
            }
            $entity->setUserId($userId);
            $entity->setFitnessProgram(self::trainingProgram[$item['type']]['id']);
            if (!is_numeric($item['number'])) {
                continue;
            }
            $entity->setNumber((int)$item['number']);
            $entity->setFitnessDate((new \DateTime())->format('Y-m-d'));
            $entityManager->persist($entity);
        }

        $entityManager->flush();
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => 1,
        ]);
    }

    /**
     * @Route("/getFitnessStatistics",name="getFitnessStatistics")
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     * @param FitnessStatisticsRepository $fitnessStatisticsRepository
     */
    public function getFitnessStatistics(RequestStack $requestStack, EntityManagerInterface $entityManager, FitnessStatisticsRepository $fitnessStatisticsRepository)
    {
        $request   = $requestStack->getMasterRequest();
        $userToken = $request->get('userToken');
        if (!($userId = self::userToken[$userToken]['userId'] ?? 0)) {
            throw new BusinessException("账号不存在");
        }

        $fitnessStatistics = $fitnessStatisticsRepository->findBy(["userId" => $userId]);
//        dd($fitnessStatistics);
        $result = array_reduce($fitnessStatistics, function ($pre, FitnessStatistics $item) {
            $pre[$item->getFitnessDate()]['fitnessDate'] = $item->getFitnessDate();
            $pre[$item->getFitnessDate()]['itemList'][]  = [
                'type'   => self::trainingProgramId[$item->getFitnessProgram()]['type'],
                'name'   => self::trainingProgramId[$item->getFitnessProgram()]['name'],
                'number' => $item->getNumber()
            ];
            return $pre;
        }, []);
        $result = $result ?? [];
        ksort($result, SORT_STRING);
        $result = array_values($result);
        return $this->json([
            'status'  => 0,
            'message' => 'success',
            'value'   => $result ,
        ]);

    }
}
