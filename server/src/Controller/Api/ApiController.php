<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/restaurants', name: 'restaurants', methods: ['GET'])]
    public function getRestaurants(): JsonResponse
    {
        $restaurants = [
            [
                'id' => 1,
                'name' => "L'Impasse Fleurie",
                'location' => 'Marais',
                'description' => 'Terrasse privée',
                'vibe' => 'Jardin caché',
                'price' => '€€€',
                'stars' => 5,
                'status' => 'Vérifié',
                'icon' => 'check',
                'accentColor' => '#CC0000',
            ],
            [
                'id' => 2,
                'name' => 'La Terrasse Cachée',
                'location' => 'Saint-Germain',
                'description' => 'Patio romantique',
                'vibe' => 'Date idéale',
                'price' => '€€€€',
                'stars' => 4,
                'status' => 'Romantique',
                'icon' => 'vibe',
                'accentColor' => '#534AB7',
            ],
            [
                'id' => 3,
                'name' => 'Saturne',
                'location' => 'Sentier',
                'description' => 'Cave naturelle',
                'vibe' => 'Naturel',
                'price' => '€€€',
                'stars' => 5,
                'status' => 'Éco',
                'icon' => 'leaf',
                'accentColor' => '#1D9E75',
            ],
            [
                'id' => 4,
                'name' => 'Frenchie Bar',
                'location' => 'Covent Garden',
                'description' => 'Bar convivial',
                'vibe' => 'Convivial',
                'price' => '€€',
                'stars' => 3,
                'status' => 'Top',
                'icon' => 'star',
                'accentColor' => '#BA7517',
            ],
        ];

        return $this->json($restaurants);
    }

    #[Route('/stats', name: 'stats', methods: ['GET'])]
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
