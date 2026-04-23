<?php

namespace App\Service;

use App\Repository\KnowledgeBaseRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VibeService
{
    private KnowledgeBaseRepository $kbRepo;
    private HttpClientInterface $httpClient;

    public function __construct(KnowledgeBaseRepository $kbRepo, HttpClientInterface $httpClient)
    {
        $this->kbRepo = $kbRepo;
        $this->httpClient = $httpClient;
    }

    /**
     * Finds matches based on a vibe string using AI.
     */
    public function findMatches(string $vibe): array
    {
        $apiKey = $_ENV['MISTRAL_API_KEY'] ?? null;
        $vibeLower = strtolower($vibe);

        // 1. Pre-selection of candidates using simple keyword matching
        $allItems = $this->kbRepo->findAll();
        $candidates = [];

        foreach ($allItems as $item) {
            $text = strtolower(
                $item->getName() . ' ' . 
                $item->getDescription() . ' ' . 
                $item->getMichelinInfo() . ' ' . 
                $item->getSearchKeywords() . ' ' .
                $item->getSpecialties() . ' ' .
                $item->getAtmosphere()
            );

            // Simple scoring for pre-selection
            $keywords = explode(' ', $vibeLower);
            $score = 0;
            foreach ($keywords as $kw) {
                if (strlen($kw) > 3 && str_contains($text, $kw)) {
                    $score++;
                }
            }

            if ($score > 0 || count($candidates) < 15) {
                $candidates[] = [
                    'item' => $item,
                    'preScore' => $score
                ];
            }
        }

        // Keep top 15 for AI analysis
        usort($candidates, fn($a, $b) => $b['preScore'] <=> $a['preScore']);
        $candidates = array_slice($candidates, 0, 15);

        if ($apiKey && strlen($apiKey) > 5) {
            return $this->matchWithAI($vibe, $candidates, $apiKey);
        }

        // Fallback to static matching if no API key
        return $this->getStaticFallback($vibe, $candidates);
    }

    private function matchWithAI(string $vibe, array $candidates, string $apiKey): array
    {
        $context = "";
        foreach ($candidates as $c) {
            $item = $c['item'];
            $context .= sprintf(
                "ID: %d | Nom: %s | Description: %s | Spécialités: %s | Ambiance: %s | Étoiles: %d\n",
                $item->getId(),
                $item->getName(),
                $item->getDescription(),
                $item->getSpecialties(),
                $item->getAtmosphere(),
                $item->getStars()
            );
        }

        $prompt = "Tu es l'Expert Concierge du Guide Michelin. Un utilisateur exprime l'envie suivante : \"$vibe\".\n" .
                  "Voici une liste de restaurants candidats sélectionnés dans notre base :\n$context\n\n" .
                  "Ta mission :\n" .
                  "1. Sélectionne les 6 meilleurs matches qui correspondent VRAIMENT à l'envie de l'utilisateur.\n" .
                  "2. Rédige une introduction personnalisée de 3 lignes expliquant ton choix en tant qu'Expert Michelin.\n" .
                  "3. Pour chaque restaurant, justifie le match en une phrase courte en mentionnant une spécialité ou l'ambiance.\n" .
                  "Réponds TOUJOURS au format JSON suivant :\n" .
                  "{\n" .
                  "  \"intro\": \"...\",\n" .
                  "  \"results\": [\n" .
                  "    { \"id\": ID, \"matchJustification\": \"...\" }\n" .
                  "  ]\n" .
                  "}";

        try {
            $response = $this->httpClient->request('POST', 'https://api.mistral.ai/v1/chat/completions', [
                'headers' => ['Authorization' => 'Bearer ' . $apiKey],
                'json' => [
                    'model' => 'mistral-tiny',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'response_format' => ['type' => 'json_object']
                ]
            ]);

            $data = json_decode($response->toArray()['choices'][0]['message']['content'], true);
            
            $finalResults = [];
            foreach ($data['results'] as $aiMatch) {
                foreach ($candidates as $c) {
                    $item = $c['item'];
                    if ($item->getId() == $aiMatch['id']) {
                        $finalResults[] = [
                            'id' => $item->getId(),
                            'name' => $item->getName(),
                            'description' => $aiMatch['matchJustification'] ?? $item->getDescription(),
                            'stars' => $item->getStars() ?? 0,
                            'score' => $item->getBaseScore(),
                            'matchStrength' => 5, // AI selected
                        ];
                        break;
                    }
                }
            }

            return [
                'vibe' => $vibe,
                'intro' => $data['intro'],
                'results' => $finalResults,
                'count' => count($finalResults)
            ];

        } catch (\Exception $e) {
            return $this->getStaticFallback($vibe, $candidates, "Désolé, mon service d'analyse IA est momentanément indisponible. Voici une sélection basée sur mes archives.");
        }
    }

    private function getStaticFallback(string $vibe, array $candidates, ?string $customIntro = null): array
    {
        $results = [];
        foreach (array_slice($candidates, 0, 6) as $c) {
            $item = $c['item'];
            $results[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'stars' => $item->getStars() ?? 0,
                'score' => $item->getBaseScore(),
                'matchStrength' => $c['preScore'],
            ];
        }

        return [
            'vibe' => $vibe,
            'intro' => $customIntro ?? "L'inspecteur Michelin a écouté votre envie de \"$vibe\". Voici les adresses sélectionnées.",
            'results' => $results,
            'count' => count($results)
        ];
    }
}
