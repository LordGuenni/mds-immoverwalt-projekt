<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Lädt die Beispieldaten, wenn noch keine Benutzer in der Datenbank sind.
 * Wird beim Start des Docker-Containers aufgerufen.
 */
class SeedIfEmptyCommand extends Command
{
    protected static $defaultName = 'app:seed-if-empty';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setDescription('Lädt die Beispieldaten, falls die Datenbank leer ist.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = (int) $this->em->createQuery('SELECT COUNT(u.id) FROM '.User::class.' u')->getSingleScalarResult();
        if ($count > 0) {
            $output->writeln('Datenbank enthält bereits Daten - nichts zu tun.');

            return 0;
        }

        if (!$this->getApplication()->has('doctrine:fixtures:load')) {
            $output->writeln('<error>DoctrineFixturesBundle nicht verfügbar (APP_ENV=dev erforderlich).</error>');

            return 1;
        }

        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $args = new ArrayInput(['--append' => true]);
        $args->setInteractive(false);

        return $command->run($args, $output);
    }
}
