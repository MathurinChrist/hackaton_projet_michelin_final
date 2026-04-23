<?php

namespace App\Controller\Api;

use App\Repository\FactCheckRepository;
use App\Repository\KnowledgeBaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/restaurants', name: 'api_restaurants_')]
class RestaurantController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        KnowledgeBaseRepository $kbRepo,
        FactCheckRepository $fcRepo
    ): JsonResponse {
        $kbItems = $kbRepo->findAll();
        $restaurants = [];

        // 1. Add Certified Michelin entries from KnowledgeBase
        foreach ($kbItems as $item) {
            $restaurants[] = [
                'id' => 'kb_' . $item->getId(),
                'name' => $item->getName(),
                'location' => 'Paris',
                'description' => $item->getDescription(),
                'vibe' => $item->getStatus() ?? 'Certifié',
                'price' => '€€€',
                'stars' => $item->getStars() ?? 0,
                'status' => 'Michelin Verified',
                'icon' => 'award',
                'accentColor' => '#BA0B2F',
                'source' => 'michelin'
            ];
        }

        // 2. Add Recent Fact-Checked entries (non-unknown)
        $fcItems = $fcRepo->findBy([], ['createdAt' => 'DESC'], 10);
        foreach ($fcItems as $fc) {
            if (str_contains(strtolower($fc->getRestaurantName()), 'inconnu')) continue;
            
            // Avoid duplicates if already in KB
            $exists = false;
            foreach ($restaurants as $r) {
                if (strtolower($r['name']) === strtolower($fc->getRestaurantName())) {
                    $exists = true; 
                    break;
                }
            }
            if ($exists) continue;

            $restaurants[] = [
                'id' => 'fc_' . $fc->getId(),
                'name' => $fc->getRestaurantName(),
                'location' => 'Vérifié via ' . $fc->getPlatform(),
                'description' => $fc->getDescription(),
                'vibe' => $fc->getVerdict() === 'Validé' ? 'Tendance' : 'Buzz Risqué',
                'price' => '€€',
                'stars' => $fc->getStars() ?? 0,
                'status' => $fc->getVerdict(),
                'icon' => $fc->getVerdict() === 'Validé' ? 'check' : 'vibe',
                'accentColor' => $fc->getVerdict() === 'Validé' ? '#1D9E75' : '#BA7517',
                'source' => 'community'
            ];
        }

        return $this->json($restaurants);
    }
}
