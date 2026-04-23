<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:seed-restaurant-sql',
    description: 'Seed restaurant table from restaurant.sql (truncate then insert)',
    aliases: ['app:import-michelin-csv']
)]
class ImportMichelinCsvCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')] private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'Path to restaurant.sql',
                $this->projectDir . '/restaurant.sql'
            )
            ->addOption(
                'truncate',
                null,
                InputOption::VALUE_NEGATABLE,
                'Truncate restaurant table before seeding',
                true
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $path = (string) $input->getArgument('path');
        if ($path === '') {
            $io->error('Missing SQL path.');
            return Command::INVALID;
        }

        $realPath = realpath($path);
        if ($realPath === false || !is_file($realPath)) {
            $io->error(sprintf('SQL file not found: %s', $path));
            return Command::FAILURE;
        }

        $io->title('Seeding restaurants from SQL');
        $io->writeln(sprintf('Source: %s', $realPath));

        $sql = file_get_contents($realPath);
        if ($sql === false || trim($sql) === '') {
            $io->error('SQL file is empty or unreadable.');
            return Command::FAILURE;
        }

        $truncate = (bool) $input->getOption('truncate');

        /** @var Connection $connection */
        $connection = $this->entityManager->getConnection();

        $connection->beginTransaction();
        try {
            if ($truncate) {
                $connection->executeStatement('TRUNCATE TABLE restaurant RESTART IDENTITY CASCADE');
            }

            $affected = $connection->executeStatement($sql);
            $connection->commit();

            $io->success(sprintf('Done. truncate=%s affected=%d', $truncate ? 'yes' : 'no', $affected));
        } catch (\Throwable $e) {
            $connection->rollBack();
            $io->error(sprintf('Seeding failed: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
