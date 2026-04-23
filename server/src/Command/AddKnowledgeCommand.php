<?php

namespace App\Command;

use App\Entity\KnowledgeBase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:add-knowledge', description: 'Entraîner l\'agent IA en ajoutant des données Michelin')]
class AddKnowledgeCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Nom de l\'établissement')
            ->addArgument('score', InputArgument::REQUIRED, 'Score Michelin /10')
            ->addArgument('keywords', InputArgument::REQUIRED, 'Mots-clés (séparés par des virgules)')
            ->addArgument('specialties', InputArgument::OPTIONAL, 'Spécialités culinaires')
            ->addArgument('atmosphere', InputArgument::OPTIONAL, 'Ambiance de l\'établissement');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $kb = new KnowledgeBase();
        $kb->setName($input->getArgument('name'));
        $kb->setBaseScore((float)$input->getArgument('score'));
        $kb->setSearchKeywords($input->getArgument('keywords'));
        $kb->setStatus($kb->getBaseScore() >= 7 ? 'Validé Michelin' : 'Risque de déception');
        $kb->setDescription('Donnée d\'entraînement ajoutée manuellement pour affiner l\'IA.');
        $kb->setMichelinInfo('Certifié par les inspecteurs.');
        $kb->setSpecialties($input->getArgument('specialties') ?: 'Spécialités en cours de référencement.');
        $kb->setAtmosphere($input->getArgument('atmosphere') ?: 'Ambiance certifiée Michelin.');

        $this->entityManager->persist($kb);
        $this->entityManager->flush();

        $io->success('L\'agent a été entraîné avec succès sur ' . $kb->getName());

        return Command::SUCCESS;
    }
}
