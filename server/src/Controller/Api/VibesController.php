<?php

namespace App\Controller\Api;

use App\Service\VibeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/vibes', name: 'api_vibes_')]
class VibesController extends AbstractController
{
    private VibeService $vibeService;

    public function __construct(VibeService $vibeService)
    {
        $this->vibeService = $vibeService;
    }

    #[Route('', name: 'search', methods: ['POST'])]
    public function search(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $vibe = $data['vibe'] ?? '';

        if (empty($vibe)) {
            return $this->json(['error' => 'Vibe is required'], 400);
        }

        $results = $this->vibeService->findMatches($vibe);

        return $this->json($results);
    }
}
