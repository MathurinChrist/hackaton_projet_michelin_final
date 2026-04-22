<?php

$dbPath = __DIR__ . '/var/data.db';
$db = new PDO('sqlite:' . $dbPath);

$data = [
    [
        'name' => 'Cédric Grolet Opéra',
        'status' => 'Viral / Pas d\'étoile',
        'michelin' => 'Sélectionné mais non étoilé.',
        'desc' => 'Buzz massif sur Instagram. Pâtisseries en trompe-l\'œil. Très cher, attente interminable (1h+).',
        'score' => 6.5,
        'keywords' => 'grolet,opera,trompe'
    ],
    [
        'name' => 'Septime',
        'status' => '1 Étoile Michelin',
        'michelin' => 'Étoilé Michelin.',
        'desc' => 'Cuisine créative, locale et éco-responsable. Le buzz est totalement mérité.',
        'score' => 9.5,
        'keywords' => 'septime,bistronomie'
    ],
    [
        'name' => 'Kodawari Tsukiji',
        'status' => 'Sélection Michelin',
        'michelin' => 'Bib Gourmand / Sélection Guide.',
        'desc' => 'Décor immersif de Tokyo. Ramen de très haute qualité.',
        'score' => 8.5,
        'keywords' => 'kodawari,tsukiji,ramen'
    ],
    [
        'name' => 'Café de Flore',
        'status' => 'Institution Historique',
        'michelin' => 'Non répertorié pour la cuisine.',
        'desc' => 'On y va pour le prestige et la terrasse, pas pour la gastronomie.',
        'score' => 4.0,
        'keywords' => 'flore,st germain'
    ]
];

foreach ($data as $item) {
    $stmt = $db->prepare("INSERT INTO knowledge_base (name, status, michelin_info, description, base_score, search_keywords) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $item['name'],
        $item['status'],
        $item['michelin'],
        $item['desc'],
        $item['score'],
        $item['keywords']
    ]);
}

echo "KnowledgeBase peuplée avec succès.\n";
