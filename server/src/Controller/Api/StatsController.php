<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/stats', name: 'api_stats_')]
class StatsController extends AbstractController
{
    #[Route('', name: 'get', methods: ['GET'])]
    public function getStats(): JsonResponse
    {
        return $this->json([
            'factChecks' => 2847,
            'satisfaction' => '94%',
            'activeExplorers' => '128k',
            'badgesObtained' => '19k',
        ]);
    }
}
