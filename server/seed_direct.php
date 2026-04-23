<?php
// Script pour injecter des données Michelin avec ÉTOILES
$dbPath = __DIR__ . '/var/data.db';
// Si vous êtes sur MySQL, ce script doit être adapté, mais je vais faire une version PDO générique.

// Version simpliste pour MySQL (à adapter selon votre .env si besoin)
// Pour le hackathon, on va continuer à alimenter ce que définit DATABASE_URL
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$dsn = $_ENV['DATABASE_URL'];
// Conversion sqlite relative -> absolute si besoin
if (str_contains($dsn, 'sqlite')) {
    $path = str_replace('sqlite:///%kernel.project_dir%/', __DIR__ . '/', $dsn);
    $db = new PDO($path);
} else {
    // MySQL : mysql://user:pass@host:port/db
    preg_match('/mysql:\/\/(.*?):(.*?)@(.*?):(\d+)\/(.*)/', $dsn, $matches);
    if ($matches) {
       $db = new PDO("mysql:host={$matches[3]};port={$matches[4]};dbname={$matches[5]}", $matches[1], $matches[2]);
    } else {
       // Fallback direct
       $db = new PDO("mysql:host=127.0.0.1;dbname=app", 'app', '!ChangeMe!');
    }
}

$data = [
    [
        'name' => 'Cédric Grolet Opéra',
        'score' => 6.5,
        'stars' => 0,
        'keywords' => 'grolet,opera,trompe,ZNRqXTJnb',
        'desc' => "Célèbre pour ses fruits en trompe-l’œil, cette adresse est une prouesse visuelle."
    ],
    [
        'name' => 'Septime',
        'score' => 9.5,
        'stars' => 1,
        'keywords' => 'septime,bistronomie,bertrand grebaut',
        'desc' => "Table emblématique de la modernité parisienne. Une cuisine d’une justesse rare."
    ],
    [
        'name' => 'Guy Savoy',
        'score' => 9.8,
        'stars' => 3,
        'keywords' => 'savoy,monnaie,gastronomique',
        'desc' => "L'excellence française à son apogée. Chaque plat est une leçon de goût et de technique."
    ],
    [
        'name' => 'Plénitude',
        'score' => 9.9,
        'stars' => 3,
        'keywords' => 'plenitude,arnaud donckele',
        'desc' => "Arnaud Donckele signe ici une partition magistrale autour des sauces et des extractions."
    ]
];

foreach ($data as $item) {
    $stmt = $db->prepare("SELECT id FROM knowledge_base WHERE name = ?");
    $stmt->execute([$item['name']]);
    if (!$stmt->fetch()) {
        $stmt = $db->prepare("INSERT INTO knowledge_base (name, base_score, stars, search_keywords, description, status) VALUES (?, ?, ?, ?, ?, 'certified')");
        $stmt->execute([$item['name'], $item['score'], $item['stars'], $item['keywords'], $item['desc']]);
    } else {
        $stmt = $db->prepare("UPDATE knowledge_base SET stars = ?, description = ?, base_score = ? WHERE name = ?");
        $stmt->execute([$item['stars'], $item['desc'], $item['score'], $item['name']]);
    }
}

echo "Base de connaissances (Étoiles incluses) mise à jour.\n";
