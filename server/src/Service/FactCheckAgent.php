<?php

namespace App\Service;

use App\Entity\FactCheck;
use App\Entity\KnowledgeBase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FactCheckAgent
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
    }

    public function analyze(string $url): array
    {
        $apiKey = $_ENV['MISTRAL_API_KEY'] ?? null;
        $steps = ["Initialisation de l'Expert Agent Michelin..."];

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception("L'URL fournie n'est pas valide.");
        }

        try {
            $steps[] = "Vérification de l'existence du lien...";
            $metaData = $this->scrapeMetaData($url);
            
            $steps[] = "Analyse sémantique isolée...";
            $restaurantName = $this->identifyRestaurant($url, $metaData, $apiKey);
            
            $steps[] = "Consultation de la KnowledgeBase Michelin...";
            $knowledge = $this->searchKnowledgeStricte($restaurantName, $url);

            $steps[] = "Finalisation du verdict expert...";
            $analysis = $this->generateDynamicVerdict($url, $restaurantName ?: "Source Inconnue", $knowledge, $apiKey);

            if ($knowledge !== null) {
                $repository = $this->entityManager->getRepository(FactCheck::class);
                $factCheck = $repository->findOneBy(['restaurantName' => $analysis['name']]);
                
                if (!$factCheck) {
                    $factCheck = new FactCheck();
                    $factCheck->setRestaurantName($analysis['name']);
                    $this->entityManager->persist($factCheck);
                }
                
                $factCheck->setUrl($url);
                $factCheck->setPlatform($this->detectPlatform($url));
                $factCheck->setVerdict($analysis['isValid'] ? 'Validé' : 'Non validé');
                $factCheck->setScore($analysis['score']);
                $factCheck->setStars($knowledge->getStars());
                $factCheck->setDescription($analysis['description']);
                
                $this->entityManager->flush();
            }

            return [
                'steps' => $steps,
                'analysis' => $analysis
            ];
        } catch (\Exception $e) {
            return [
                'steps' => $steps,
                'analysis' => $this->getErrorAnalysis($e->getMessage())
            ];
        }
    }

    private function generateDynamicVerdict(string $url, string $name, ?KnowledgeBase $kb, ?string $key): array
    {
        $score = $kb ? $kb->getBaseScore() : 4.5;
        $restaurantName = $kb ? $kb->getName() : $name;
        $description = $kb ? $kb->getDescription() : "L'agent Michelin n'a pas trouvé cet établissement dans sa base certifiée. Prudence recommandée.";

        // Détermination du CONSEIL (Advisory)
        if ($score >= 8.5) {
            $advisory = "FONCEZ-Y ! Une adresse d'exception validée par nos inspecteurs.";
            $advisoryColor = "#1D9E75"; // Vert Michelin
        } elseif ($score >= 7.0) {
            $advisory = "À TESTER. Une expérience intéressante, malgré quelques irrégularités.";
            $advisoryColor = "#BA7517"; // Or
        } else {
            $advisory = "PRUDENCE. Le buzz semble primer sur l'assiette. Nous déconseillons.";
            $advisoryColor = "#BA0B2F"; // Rouge Michelin
        }

        if ($kb && $key && strlen($key) > 5) {
            try {
                $prompt = "Rédige une critique Michelin de 2 lignes pour '$restaurantName' (Note: $score/10). " .
                          "Le ton doit être expert et cohérent avec la note. Finis par un verdict.";
                $response = $this->httpClient->request('POST', 'https://api.mistral.ai/v1/chat/completions', [
                    'headers' => ['Authorization' => 'Bearer ' . $key],
                    'json' => [
                        'model' => 'mistral-tiny',
                        'messages' => [['role' => 'user', 'content' => $prompt]]
                    ]
                ]);
                $data = $response->toArray();
                $description = trim($data['choices'][0]['message']['content'], " \n\r\t\v\0.\"");
            } catch (\Exception $e) {}
        }

        return [
            'name' => $restaurantName,
            'location' => 'Vérifié',
            'description' => $description,
            'advisory' => $advisory,
            'advisoryColor' => $advisoryColor,
            'stars' => $kb ? $kb->getStars() : 0,
            'score' => $score,
            'isValid' => $score >= 7.5,
            'platform' => $this->detectPlatform($url),
            'views' => 'Analyse Temps-Réel',
            'alternative' => ['name' => 'Septime', 'location' => 'Paris', 'type' => 'La Valeur Sûre']
        ];
    }

    private function identifyRestaurant(string $url, array $meta, ?string $key): string
    {
        $content = ($meta['title'] ?? '') . ' ' . ($meta['description'] ?? '');
        $hName = $this->heuristicExtraction($url . ' ' . $content);
        if ($hName) return $hName;

        if ($key && strlen($key) > 5) {
            try {
                $response = $this->httpClient->request('POST', 'https://api.mistral.ai/v1/chat/completions', [
                    'headers' => ['Authorization' => 'Bearer ' . $key],
                    'json' => [
                        'model' => 'mistral-tiny',
                        'messages' => [['role' => 'user', 'content' => "Réponds uniquement par le NOM du restaurant présenté ici. Texte : $content (URL: $url)"]]
                    ]
                ]);
                $res = trim($response->toArray()['choices'][0]['message']['content'], " \n\r\t\v\0.\"");
                return $res !== 'INCONNU' ? $res : "";
            } catch (\Exception $e) {}
        }
        return "";
    }

    private function searchKnowledgeStricte(string $name, string $url): ?KnowledgeBase
    {
        $repository = $this->entityManager->getRepository(KnowledgeBase::class);
        $all = $repository->findAll();
        $nameLower = strtolower($name);
        $urlLower = strtolower($url);

        foreach ($all as $kb) {
            $kbName = strtolower($kb->getName());
            if ($name !== '' && ($nameLower === $kbName || str_contains($nameLower, $kbName))) return $kb;
            $keywords = explode(',', $kb->getSearchKeywords());
            foreach ($keywords as $kw) {
                $kwTrim = strtolower(trim($kw));
                if (strlen($kwTrim) < 4 || in_array($kwTrim, ['video', 'tiktok', 'paris'])) continue;
                if (str_contains($urlLower, $kwTrim)) return $kb;
            }
        }
        return null;
    }

    private function scrapeMetaData(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'],
                'timeout' => 5 
            ]);
            $html = $response->getContent(false);
            preg_match('/<title>(.*?)<\/title>/', $html, $m1);
            preg_match('/property="og:description" content="(.*?)"/', $html, $m2);
            return [
                'title' => $m1[1] ?? '',
                'description' => $m2[1] ?? ''
            ];
        } catch (\Exception $e) {}
        return [];
    }

    private function detectPlatform(string $url): string
    {
        if (str_contains($url, 'tiktok')) return 'TikTok';
        if (str_contains($url, 'instagram')) return 'Instagram';
        return 'Web';
    }

    private function heuristicExtraction(string $text): string
    {
        $t = strtolower($text);
        if (str_contains($t, 'grolet')) return 'Cédric Grolet Opéra';
        if (str_contains($t, 'septime')) return 'Septime';
        if (str_contains($t, 'savoy')) return 'Guy Savoy';
        if (str_contains($t, 'plenitude')) return 'Plénitude';
        return "";
    }

    private function getErrorAnalysis(string $msg): array
    {
        return [
            'name' => 'Analyse Impossible',
            'location' => 'Erreur',
            'description' => $msg,
            'advisory' => 'ERREUR SYSTEME',
            'advisoryColor' => '#000000',
            'score' => 0,
            'stars' => 0,
            'isValid' => false,
            'platform' => 'Internal',
            'views' => 'N/A',
            'alternative' => ['name' => 'Support', 'location' => 'Contact', 'type' => 'Technique']
        ];
    }
}
