<?php
    
    namespace App\Service;
    
    use App\Entity\KnowledgeBase;
    use App\Entity\FactCheck;
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
                // Détection du type d'URL
                $steps[] = "🔍 Analyse du type d'URL...";
                
                // Si c'est une URL TikTok
                if (str_contains($url, 'tiktok.com')) {
                    return $this->analyzeTikTokUrl($url, $apiKey, $steps);
                }
                
                // Pour les autres plateformes
                return $this->analyzeGenericUrl($url, $apiKey, $steps);
                
            } catch (\Exception $e) {
                return [
                    'steps' => $steps,
                    'analysis' => $this->getErrorAnalysis($e->getMessage())
                ];
            }
        }
        
        /**
         * Analyse spécifique pour TikTok
         */
        private function analyzeTikTokUrl(string $url, ?string $apiKey, array $steps): array
        {
            $steps[] = "📱 Détection d'une URL TikTok";
            
            // Extraire l'ID de la vidéo
            $videoId = $this->extractTikTokVideoId($url);
            
            if ($videoId) {
                $steps[] = "✅ ID vidéo extrait : " . $videoId;
                
                // Tentative 1: API oEmbed
                $steps[] = "📡 Tentative via oEmbed API...";
                $videoData = $this->getTikTokOEmbedData($videoId);
                
                if ($videoData && !empty($videoData['description'])) {
                    $steps[] = "✅ Contenu récupéré via oEmbed";
                    return $this->processAnalysis($videoData, $url, $apiKey, $steps);
                }
                
                // Tentative 2: API alternative
                $steps[] = "🔄 Tentative via API alternative...";
                $videoData = $this->getTikTokAlternativeData($videoId);
                
                if ($videoData && !empty($videoData['description'])) {
                    $steps[] = "✅ Contenu récupéré via API alternative";
                    return $this->processAnalysis($videoData, $url, $apiKey, $steps);
                }
            }
            
            // Si tout échoue, on retourne une réponse qui indique qu'il faut utiliser l'URL complète
            $steps[] = "❌ Extraction automatique impossible";
            $steps[] = "💡 Utilisez l'URL complète TikTok (pas de lien court)";
            
            return [
                'steps' => $steps,
                'analysis' => [
                    'name' => 'URL TikTok non compatible',
                    'isKnown' => false,
                    'location' => 'N/A',
                    'description' => "## ⚠️ URL TikTok non supportée\n\n"
                        . "Les liens courts (`vm.tiktok.com`) ne sont pas supportés en raison des restrictions de TikTok.\n\n"
                        . "**Solution :**\n\n"
                        . "1. Ouvrez la vidéo dans votre navigateur\n"
                        . "2. Copiez l'URL complète depuis la barre d'adresse\n"
                        . "3. Utilisez une URL au format :\n"
                        . "   `https://www.tiktok.com/@username/video/1234567890123456789`\n\n"
                        . "**Exemple d'URL valide :**\n"
                        . "`https://www.tiktok.com/@silkstoriesofmine/video/7431664757662338337`",
                    'advisory' => '⚠️ Action requise',
                    'advisoryColor' => '#FFA500',
                    'score' => 0,
                    'stars' => 0,
                    'isValid' => false,
                    'platform' => 'TikTok',
                    'analysisMethod' => 'URL non supportée',
                    'iaConfidence' => 0,
                    'iaReasoning' => 'URL courte non supportée',
                    'verdict' => 'Non validé'
                ]
            ];
        }
        
        /**
         * Analyse pour les URLs génériques
         */
        private function analyzeGenericUrl(string $url, ?string $apiKey, array $steps): array
        {
            $steps[] = "🔗 Extraction du contenu...";
            $videoData = $this->extractGenericContent($url);
            
            if (empty($videoData['description']) && empty($videoData['fullText'])) {
                $steps[] = "❌ Aucun contenu extrait";
                return [
                    'steps' => $steps,
                    'analysis' => $this->getErrorAnalysis("Impossible d'extraire le contenu de l'URL")
                ];
            }
            
            return $this->processAnalysis($videoData, $url, $apiKey, $steps);
        }
        
        /**
         * Extrait l'ID d'une vidéo TikTok
         */
        private function extractTikTokVideoId(string $url): ?string
        {
            // Format standard: https://www.tiktok.com/@username/video/1234567890123456789
            if (preg_match('/tiktok\.com\/@[\w.-]+\/video\/(\d+)/', $url, $matches)) {
                return $matches[1];
            }
            
            // Format avec paramètres
            if (preg_match('/video\/(\d+)/', $url, $matches)) {
                return $matches[1];
            }
            
            // Pour les URLs courtes, on ne peut pas extraire l'ID directement
            if (str_contains($url, 'vm.tiktok.com')) {
                return null;
            }
            
            return null;
        }
        
        /**
         * Récupère les données via l'API oEmbed TikTok
         */
        private function getTikTokOEmbedData(string $videoId): ?array
        {
            try {
                // Construction de l'URL oEmbed
                $videoUrl = "https://www.tiktok.com/@/video/{$videoId}";
                $oembedUrl = "https://www.tiktok.com/oembed?url=" . urlencode($videoUrl);
                
                $response = $this->httpClient->request('GET', $oembedUrl, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ],
                    'timeout' => 10
                ]);
                
                if ($response->getStatusCode() === 200) {
                    $data = $response->toArray();
                    
                    $description = strip_tags($data['title'] ?? '');
                    $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
                    
                    if (!empty($description)) {
                        return [
                            'description' => $description,
                            'author' => $data['author_name'] ?? '',
                            'fullText' => $description,
                            'title' => $description,
                            'hashtags' => $this->extractHashtags($description),
                            'url' => $videoUrl
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Continue
            }
            
            return null;
        }
        
        /**
         * API alternative pour TikTok
         */
        private function getTikTokAlternativeData(string $videoId): ?array
        {
            // Utiliser l'API publique de TikTok (si accessible)
            $apiUrl = "https://www.tiktok.com/node/share/video/@/{$videoId}";
            
            try {
                $response = $this->httpClient->request('GET', $apiUrl, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ],
                    'timeout' => 10
                ]);
                
                if ($response->getStatusCode() === 200) {
                    $data = $response->toArray();
                    
                    if (isset($data['itemInfo']['itemStruct']['desc'])) {
                        $description = $data['itemInfo']['itemStruct']['desc'];
                        $author = $data['itemInfo']['itemStruct']['author']['uniqueId'] ?? '';
                        
                        return [
                            'description' => $description,
                            'author' => $author,
                            'fullText' => $description,
                            'title' => $description,
                            'hashtags' => $this->extractHashtags($description),
                            'url' => "https://www.tiktok.com/@{$author}/video/{$videoId}"
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Continue
            }
            
            return null;
        }
        
        /**
         * Extrait le contenu générique d'une URL
         */
        private function extractGenericContent(string $url): array
        {
            $data = [
                'description' => '',
                'fullText' => '',
                'title' => '',
                'hashtags' => [],
                'author' => ''
            ];
            
            try {
                $response = $this->httpClient->request('GET', $url, [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                    ],
                    'timeout' => 15
                ]);
                
                $html = $response->getContent(false);
                
                // Extraire la description
                if (preg_match('/<meta name="description" content="([^"]+)"/', $html, $match)) {
                    $data['description'] = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
                }
                
                // Extraire le titre
                if (preg_match('/<title>(.*?)<\/title>/', $html, $match)) {
                    $data['title'] = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
                }
                
                $data['fullText'] = $data['title'] . ' ' . $data['description'];
                $data['hashtags'] = $this->extractHashtags($data['fullText']);
                
            } catch (\Exception $e) {
                // Continue avec données vides
            }
            
            return $data;
        }
        
        /**
         * Extrait les hashtags d'un texte
         */
        private function extractHashtags(string $text): array
        {
            preg_match_all('/#([A-Za-zÀ-ÿ0-9_]+)/u', $text, $matches);
            return array_unique($matches[1] ?? []);
        }
        
        /**
         * Traite l'analyse complète
         */
        private function processAnalysis(array $videoData, string $url, ?string $apiKey, array $steps): array
        {
            $steps[] = "🧠 Analyse par Mistral AI...";
            $analysisResult = $this->deepAnalyzeWithMistral($videoData, $apiKey);
            
            $steps[] = "🎯 Restaurant identifié : " . ($analysisResult['restaurantName'] ?: "Aucun");
            $steps[] = "📝 Confiance : " . ($analysisResult['confidence'] ?? 0) . "%";
            $steps[] = "💬 Raisonnement : " . substr($analysisResult['reasoning'] ?? '', 0, 100);
            
            // Recherche dans la base de connaissances
            if ($analysisResult['confidence'] >= 50 && $analysisResult['restaurantName'] !== 'INCONNU') {
                $steps[] = "📚 Recherche dans la base Michelin...";
                $knowledge = $this->searchKnowledgeStricte($analysisResult['restaurantName'], $url);
                $steps[] = $knowledge ? "✅ Restaurant trouvé en base" : "❌ Restaurant non référencé";
            } else {
                $knowledge = null;
                $steps[] = "⚠️ Confiance insuffisante ou restaurant non identifié";
            }
            
            $steps[] = "📋 Génération du verdict...";
            $analysis = $this->generateDynamicVerdict($url, $analysisResult['restaurantName'], $knowledge, $apiKey, $analysisResult);
            
            // Sauvegarder en base si pertinent
            if ($analysisResult['restaurantName'] !== 'INCONNU') {
                $this->saveToDatabase($url, $analysis, $analysisResult, $videoData);
            }
            
            return [
                'steps' => $steps,
                'analysis' => $analysis,
                'iaReasoning' => $analysisResult
            ];
        }
        
        /**
         * Analyse approfondie avec Mistral
         */
        private function deepAnalyzeWithMistral(array $content, ?string $apiKey): array
        {
            if (!$apiKey) {
                return [
                    'restaurantName' => 'INCONNU',
                    'confidence' => 0,
                    'reasoning' => 'Clé API Mistral manquante'
                ];
            }
            
            $textToAnalyze = $content['fullText'] ?? $content['description'] ?? '';
            
            if (empty($textToAnalyze)) {
                return [
                    'restaurantName' => 'INCONNU',
                    'confidence' => 0,
                    'reasoning' => 'Aucun texte à analyser'
                ];
            }
            
            $prompt = "Extrais le nom du restaurant mentionné dans ce texte.\n\n"
                . "TEXTE: \"{$textToAnalyze}\"\n\n"
                . "RÈGLES:\n"
                . "- Ne réponds que si un NOM DE RESTAURANT SPÉCIFIQUE est clairement mentionné\n"
                . "- Ignore les mots génériques: restaurant, paris, food, manger, bon, delicieux, tiktok\n"
                . "- Si aucun nom clair, réponds INCONNU\n\n"
                . "Format de réponse EXACT:\n"
                . "RESTAURANT: [nom ou INCONNU]\n"
                . "CONFIANCE: [0-100]\n"
                . "RAISONNEMENT: [une phrase]";
            
            try {
                $response = $this->httpClient->request('POST', 'https://api.mistral.ai/v1/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'model' => 'mistral-tiny',
                        'messages' => [
                            ['role' => 'system', 'content' => 'Tu es un expert en extraction de noms de restaurants. Tu es strict et tu n\'inventes rien.'],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'temperature' => 0.1,
                        'max_tokens' => 200
                    ]
                ]);
                
                $data = $response->toArray();
                $responseText = $data['choices'][0]['message']['content'];
                
                return $this->parseMistralResponse($responseText);
                
            } catch (\Exception $e) {
                return [
                    'restaurantName' => 'INCONNU',
                    'confidence' => 0,
                    'reasoning' => 'Erreur API: ' . $e->getMessage()
                ];
            }
        }
        
        /**
         * Parse la réponse de Mistral
         */
        private function parseMistralResponse(string $response): array
        {
            $result = [
                'restaurantName' => 'INCONNU',
                'confidence' => 0,
                'reasoning' => $response
            ];
            
            if (preg_match('/RESTAURANT:\s*(.+)/i', $response, $matches)) {
                $name = trim($matches[1]);
                if (strtoupper($name) !== 'INCONNU' && strlen($name) > 2) {
                    $result['restaurantName'] = $this->cleanRestaurantName($name);
                }
            }
            
            if (preg_match('/CONFIANCE:\s*(\d+)/i', $response, $matches)) {
                $result['confidence'] = min(100, max(0, (int)$matches[1]));
            }
            
            if (preg_match('/RAISONNEMENT:\s*(.+)/is', $response, $matches)) {
                $result['reasoning'] = trim($matches[1]);
            }
            
            return $result;
        }
        
        /**
         * Recherche dans la base de connaissances
         */
        private function searchKnowledgeStricte(string $name, string $url): ?KnowledgeBase
        {
            $invalidNames = ['INCONNU', 'RESTAURANT NON IDENTIFIÉ', '', 'AUCUN'];
            if (empty($name) || in_array(strtoupper($name), $invalidNames)) {
                return null;
            }
            
            $repository = $this->entityManager->getRepository(KnowledgeBase::class);
            $allRestaurants = $repository->findAll();
            
            if (empty($allRestaurants)) {
                return null;
            }
            
            $searchName = strtolower(trim($name));
            
            foreach ($allRestaurants as $restaurant) {
                $restoName = strtolower(trim($restaurant->getName()));
                
                // Match exact
                if ($searchName === $restoName) {
                    return $restaurant;
                }
                
                // Match partiel
                if (str_contains($searchName, $restoName) || str_contains($restoName, $searchName)) {
                    return $restaurant;
                }
                
                // Recherche par mots-clés
                $keywords = explode(',', $restaurant->getSearchKeywords());
                foreach ($keywords as $keyword) {
                    $keyword = strtolower(trim($keyword));
                    if (strlen($keyword) > 2 && str_contains($searchName, $keyword)) {
                        return $restaurant;
                    }
                }
            }
            
            return null;
        }
        
        /**
         * Génère le verdict dynamique
         */
        private function generateDynamicVerdict(string $url, string $name, ?KnowledgeBase $kb, ?string $apiKey, ?array $iaAnalysis = null): array
        {
            $isKnown = $kb !== null && $name !== 'INCONNU';
            $restaurantName = $kb ? $kb->getName() : ($name === 'INCONNU' ? 'Source Inconnue' : $name);
            
            if ($isKnown) {
                $score = $kb->getBaseScore();
                $description = $kb->getDescription();
                $stars = $kb->getStars();
                $location = $kb->getSearchKeywords();
                $verdict = $score >= 7 ? 'Validé' : 'Non validé';
                
                if ($score >= 8.5) {
                    $advisory = "⭐ ÉTOILE MICHELIN - Restaurant d'exception validé par nos inspecteurs !";
                    $color = "#1D9E75";
                } elseif ($score >= 7.0) {
                    $advisory = "🍽️ BIB GOURMAND - Excellent rapport qualité-prix, recommandé par le Guide Michelin.";
                    $color = "#BA7517";
                } elseif ($score >= 5.0) {
                    $advisory = "ℹ️ ASSIETTE MICHELIN - Table correcte, quelques points à améliorer.";
                    $color = "#FFA500";
                } else {
                    $advisory = "⚠️ NON RECOMMANDÉ - Notre équipe déconseille cet établissement.";
                    $color = "#BA0B2F";
                }
            } else {
                $score = 0;
                $stars = 0;
                $location = 'Paris';
                $verdict = 'Non validé';
                $confidence = $iaAnalysis['confidence'] ?? 0;
                $iaReasoning = $iaAnalysis['reasoning'] ?? '';
                
                if ($name === 'INCONNU') {
                    $description = "Aucun nom de restaurant n'a pu être extrait de la vidéo. Texte analysé: \"" . substr($iaReasoning, 0, 100) . "\"";
                    $advisory = "⚠️ AUCUN RESTAURANT IDENTIFIÉ";
                } else {
                    $description = "Restaurant identifié par l'IA avec {$confidence}% de confiance. Raisonnement: {$iaReasoning}";
                    $advisory = "⚠️ RESTAURANT NON RÉFÉRENCÉ DANS LA BASE MICHELIN";
                }
                $color = "#BA0B2F";
            }
            
            return [
                'name' => $restaurantName,
                'isKnown' => $isKnown,
                'location' => $location,
                'description' => $description,
                'advisory' => $advisory,
                'advisoryColor' => $color,
                'stars' => $stars,
                'score' => $score,
                'isValid' => $isKnown && $score >= 7.0,
                'platform' => $this->detectPlatform($url),
                'analysisMethod' => 'Mistral IA Deep Analysis',
                'iaConfidence' => $iaAnalysis['confidence'] ?? 0,
                'iaReasoning' => $iaAnalysis['reasoning'] ?? '',
                'verdict' => $verdict
            ];
        }
        
        /**
         * Sauvegarde en base de données (sans méthodes supplémentaires)
         */
        private function saveToDatabase(string $url, array $analysis, array $iaAnalysis, array $videoData): void
        {
            try {
                $factCheck = new FactCheck();
                $factCheck->setUrl($url);
                $factCheck->setRestaurantName($analysis['name']);
                $factCheck->setPlatform($analysis['platform']);
                $factCheck->setDescription($analysis['description']);
                $factCheck->setScore($analysis['score']);
                $factCheck->setStars($analysis['stars']);
                $factCheck->setVerdict($analysis['verdict']);
                $factCheck->setCreatedAt(new \DateTimeImmutable());
                
                $this->entityManager->persist($factCheck);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                // Ne pas bloquer l'analyse si la sauvegarde échoue
            }
        }
        
        /**
         * Détecte la plateforme
         */
        private function detectPlatform(string $url): string
        {
            if (str_contains($url, 'tiktok.com')) return 'TikTok';
            if (str_contains($url, 'instagram.com')) return 'Instagram';
            if (str_contains($url, 'youtube.com')) return 'YouTube';
            return 'Web';
        }
        
        /**
         * Nettoie le nom du restaurant
         */
        private function cleanRestaurantName(string $name): string
        {
            $name = preg_replace('/[^\p{L}\p{N}\s\'-]/u', '', $name);
            $name = preg_replace('/\s+/', ' ', $name);
            $name = trim($name);
            
            $words = explode(' ', $name);
            $words = array_map(fn($w) => mb_convert_case($w, MB_CASE_TITLE, 'UTF-8'), $words);
            
            return implode(' ', $words);
        }
        
        /**
         * Retourne une analyse d'erreur
         */
        private function getErrorAnalysis(string $msg): array
        {
            return [
                'name' => 'Source Inconnue',
                'isKnown' => false,
                'location' => 'Paris',
                'description' => $msg,
                'advisory' => 'ERREUR TECHNIQUE',
                'advisoryColor' => '#BA0B2F',
                'score' => 0,
                'stars' => 0,
                'isValid' => false,
                'platform' => 'Error',
                'analysisMethod' => 'Mistral IA',
                'iaConfidence' => 0,
                'iaReasoning' => $msg,
                'verdict' => 'Non validé'
            ];
        }
    }