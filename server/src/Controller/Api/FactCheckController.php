<?php

namespace App\Controller\Api;

use App\Repository\FactCheckRepository;
use App\Service\FactCheckAgent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/fact-check', name: 'api_fact_check_')]
class FactCheckController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(FactCheckRepository $repository): JsonResponse
    {
        $recent = $repository->findBy([], ['createdAt' => 'DESC'], 6);
        
        $formatted = [];
        foreach ($recent as $fc) {
            if (str_contains(strtolower($fc->getRestaurantName()), 'inconnu')) {
                continue;
            }
            
            $formatted[] = [
                'id' => $fc->getId(),
                'platform' => $fc->getPlatform(),
                'stats' => 'Archive Certifiée',
                'status' => $fc->getVerdict(),
                'statusType' => $fc->getVerdict() === 'Validé' ? 'success' : 'error',
                'name' => $fc->getRestaurantName(),
                'location' => 'Paris',
                'description' => $fc->getDescription(),
                'score' => $fc->getScore(),
                'stars' => $fc->getStars() ?? 0,
                'accent' => $fc->getVerdict() === 'Validé' ? '#1D9E75' : '#BA0B2F'
            ];
        }

        return $this->json($formatted);
    }

    #[Route('', name: 'analyze', methods: ['POST'])]
    public function analyze(
        Request $request,
        FactCheckAgent $agent,
        FactCheckRepository $repository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $url = $data['url'] ?? '';

        // Simulation LLM latency
        usleep(1000000); 

        $result = $agent->analyze($url);
        
        // Fetch real recent entries from DB
        $recent = $repository->findBy([], ['createdAt' => 'DESC'], 3);
        $formattedRecent = array_map(function($fc) {
            return [
                'id' => $fc->getId(),
                'platform' => $fc->getPlatform(),
                'stats' => 'Récent',
                'status' => $fc->getVerdict(),
                'statusType' => $fc->getVerdict() === 'Validé' ? 'success' : 'error',
                'name' => $fc->getRestaurantName(),
                'location' => 'Paris',
                'description' => $fc->getDescription(),
                'score' => $fc->getScore(),
                'stars' => $fc->getStars() ?? 0,
                'accent' => $fc->getVerdict() === 'Validé' ? '#1D9E75' : '#BA0B2F'
            ];
        }, $recent);

        return $this->json([
            'status' => 'success',
            'steps' => $result['steps'],
            'analysis' => $result['analysis'],
            'recent' => $formattedRecent
        ]);
    }
}
