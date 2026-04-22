<?php

use App\Entity\FactCheck;
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

require __DIR__.'/vendor/autoload.php';

$kernel = new Kernel('dev', true);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

$data = [
    ['url' => 'https://tiktok.com/@cedricgrolet/video/1', 'name' => 'Cédric Grolet Opéra', 'platform' => 'TikTok', 'score' => 6.5, 'verdict' => 'Non validé', 'desc' => 'Beaucoup d\'attente pour un résultat très photogénique mais cher.'],
    ['url' => 'https://insta.com/septime', 'name' => 'Septime', 'platform' => 'Instagram', 'score' => 9.5, 'verdict' => 'Validé', 'desc' => 'L\'excellence étoilée, le buzz est totalement mérité.'],
    ['url' => 'https://maps.google.com/kodawari', 'name' => 'Kodawari Tsukiji', 'platform' => 'Web', 'score' => 8.5, 'verdict' => 'Validé', 'desc' => 'Immersion totale et ramens exceptionnels.'],
    ['url' => 'https://tiktok.com/@foodie/flore', 'name' => 'Café de Flore', 'platform' => 'TikTok', 'score' => 4.0, 'verdict' => 'Non validé', 'desc' => 'Une institution pour la photo, pas pour le rapport qualité/prix.'],
];

foreach ($data as $item) {
    $fc = new FactCheck();
    $fc->setUrl($item['url']);
    $fc->setRestaurantName($item['name']);
    $fc->setPlatform($item['platform']);
    $fc->setScore($item['score']);
    $fc->setVerdict($item['verdict']);
    $fc->setDescription($item['desc']);
    $em->persist($fc);
}

$em->flush();
echo "Base de données peuplée avec du contenu RÉEL.\n";
