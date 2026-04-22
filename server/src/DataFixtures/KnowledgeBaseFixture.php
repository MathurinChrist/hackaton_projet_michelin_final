<?php

namespace App\DataFixtures;

use App\Entity\KnowledgeBase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;

class KnowledgeBaseFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $data = $this->loadFromJson(__DIR__ . '/../../data_ia/knowledge_base.json');

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

            $manager->persist($kb);
        }

        $manager->flush();
    }

    private function loadFromJson(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException(sprintf(
                'Knowledge base JSON file not found at: %s',
                $filePath
            ));
        }

        $json = file_get_contents($filePath);

        if ($json === false) {                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
            throw new RuntimeException(sprintf(
                'Failed to read knowledge base JSON file at: %s',
                $filePath
            ));
        }

        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(sprintf(
                'Invalid JSON in knowledge base file: %s',
                json_last_error_msg()
            ));
        }

        return $data;
    }
}