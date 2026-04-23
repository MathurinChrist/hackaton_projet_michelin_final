<?php

use App\Entity\KnowledgeBase;
use App\Kernel;

require __DIR__.'/vendor/autoload.php';

$kernel = new Kernel('prod', false);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

$filePath = __DIR__ . '/data_ia/knowledge_base.json';

if (!file_exists($filePath)) {
    die("Error: JSON file not found at $filePath\n");
}

$json = file_get_contents($filePath);
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid JSON: " . json_last_error_msg() . "\n");
}

foreach ($data as $item) {
    $kb = new KnowledgeBase();
    $kb->setName($item['name']);
    $kb->setStatus($item['status']);
    $kb->setStars($item['stars']);
    $kb->setMichelinInfo($item['michelinInfo']);
    $kb->setDescription($item['description']);
    $kb->setBaseScore($item['baseScore']);
    $kb->setSearchKeywords($item['searchKeywords']);
    $kb->setSpecialties($item['specialties'] ?? null);
    $kb->setAtmosphere($item['atmosphere'] ?? null);

    $em->persist($kb);
}

$em->flush();
echo "Base de connaissances chargée avec succès depuis le JSON.\n";
