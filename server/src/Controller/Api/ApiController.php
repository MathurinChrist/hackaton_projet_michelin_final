<?php

namespace App\Controller\Api;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/restaurants', name: 'restaurants', methods: ['GET'])]
    public function getRestaurants(Request $request, RestaurantRepository $restaurantRepository): JsonResponse
    {
        $limit = (int) $request->query->get('limit', 200);
        $offset = (int) $request->query->get('offset', 0);

        $restaurants = $restaurantRepository->findPaginated($limit, $offset);

        $payload = array_map(static function (Restaurant $r): array {
            $award = $r->getAward();
            $stars = self::awardToStars($award);

            $icon = $r->isGreenStar()
                ? 'leaf'
                : ($stars > 0 ? 'star' : 'award');

            $accentColor = $r->isGreenStar()
                ? '#1D9E75'
                : ($stars === 3 ? '#CC0000' : ($stars === 2 ? '#534AB7' : '#BA7517'));

            return [
                'id' => $r->getId(),
                'name' => $r->getName(),
                'location' => $r->getLocation(),
                'description' => $r->getDescription(),
                'vibe' => $r->getCuisine(),
                'price' => $r->getPrice(),
                'stars' => $stars,
                'status' => $award ?? 'Selected',
                'icon' => $icon,
                'accentColor' => $accentColor,

                // Extra dataset fields (useful for maps/details)
                'address' => $r->getAddress(),
                'cuisine' => $r->getCuisine(),
                'award' => $award,
                'greenStar' => $r->isGreenStar(),
                'longitude' => $r->getLongitude(),
                'latitude' => $r->getLatitude(),
                'phoneNumber' => $r->getPhoneNumber(),
                'url' => $r->getUrl(),
                'websiteUrl' => $r->getWebsiteUrl(),
                'facilitiesAndServices' => $r->getFacilitiesAndServices(),
            ];
        }, $restaurants);

        return $this->json($payload);
    }

    private static function awardToStars(?string $award): int
    {
        if ($award === null) {
            return 0;
        }

        $award = strtolower($award);
        if (str_contains($award, '3') && str_contains($award, 'star')) {
            return 3;
        }
        if (str_contains($award, '2') && str_contains($award, 'star')) {
            return 2;
        }
        if (str_contains($award, '1') && str_contains($award, 'star')) {
            return 1;
        }

        return 0;
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
