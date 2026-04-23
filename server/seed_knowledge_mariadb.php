<?php

use App\Entity\KnowledgeBase;
use App\Kernel;

require __DIR__.'/vendor/autoload.php';

$kernel = new Kernel('prod', false);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

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
    $kb = new KnowledgeBase();
    $kb->setName($item['name']);
    $kb->setStatus($item['status']);
    $kb->setMichelinInfo($item['michelin']);
    $kb->setDescription($item['desc']);
    $kb->setBaseScore($item['score']);
    $kb->setSearchKeywords($item['keywords']);
    $em->persist($kb);
}

$em->flush();
echo "KnowledgeBase peuplée avec succès.\n";
