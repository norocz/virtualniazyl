<?php

namespace App\Command;

use AllowDynamicProperties;
use App\Model\Orm\Repository\UsersRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"

#[AsCommand(
    name: 'app:stats',
    description: 'Informace o stavu aplikace.',
    hidden: false,
    aliases: ['a:s']
)]

class StatsCommand extends Command
{
    public function __construct()
    {
        parent::__construct();


    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $section1 = $output->section();
        $section2 = $output->section();
        $section3 = $output->section();

        $section1->writeln('Stav aplikace Virtualni azyl');
        sleep(1);
        $section2->writeln(date('d.m.Y H:i:s'));
        sleep(1);
        $section3->writeln('Tahám data');
        $section3->overwrite('Generuji výstup');
        $section3->overwrite('Generuji výstup | ');
        $section3->overwrite('Generuji výstup / ');
        $section3->overwrite('Generuji výstup | ');
        $section3->overwrite('Generuji výstup \ ');
        sleep(1);
        $section3->clear();



        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}