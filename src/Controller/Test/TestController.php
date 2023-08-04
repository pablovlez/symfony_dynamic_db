<?php

/*
 * (c) NovarWare <desarrollo@novarsoft.co>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Test;

use App\DBAL\MultiDbConnectionWrapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test', name: 'test_')]
class TestController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/connect', name: 'connect', methods: ['POST'])]
    public function sendEmail(Request $request): JsonResponse
    {
        $connection = $this->entityManager->getConnection();
        if (!$connection instanceof MultiDbConnectionWrapper) {
            throw new \RuntimeException('Wrong connection');
        }

        $data = json_decode($request->getContent(), true);
        $dbName = $data['dbname'];
        $user = $data['user'];
        $pass = $data['password'];
        $host = $data['host'];
        $port = $data['port'];

        $connection->selectDatabase($dbName, $user, $pass, $host, $port);

        $sql = 'SELECT * FROM usuario';
        $stmt = $connection->prepare($sql);
        $resultSet = $stmt->executeQuery();
        $registros = $resultSet->fetchAllAssociative();

        return $this->json([$registros]);
    }
}
