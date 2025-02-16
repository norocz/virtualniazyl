<?php

namespace App\Command;

use App\Model\Orm\Repository\UsersRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Test extends Command
{
    protected static $defaultName = 'app:test';
    protected static $defaultDescription = 'Můj vlastní příkaz pro Nette konzoli.';

    private $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        parent::__construct();
        $this->usersRepository = $usersRepository;
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $section1 = $output->section();
        $section2 = $output->section();
        $section3 = $output->section();

        $users = $this->usersRepository->findAll();

        $section1->writeln('Stav aplikace Virtualni azyl');
        $section2->writeln(date('d.m.Y H:i:s'));
        sleep(1);

        foreach ($users as $user) {

            $section3->writeln('+-----------------------------------------------+');
            $section3->writeln($user->getCreatedAt()->format('d.m.Y H:i:s'));
            $section3->writeln($user->getUsername());
            $section3->writeln($user->getEmail());
            $section3->writeln($user->getPhone());
            $section3->writeln('+-----------------------------------------------+');
            $messages = $user->getSentMessages();
            if (!empty($messages)) {
            foreach ($messages as $message) {
                $section3->writeln($message->getCreatedAt()->format('d.m.Y H:i:s'));
                $section3->writeln($message->getMessage());

                }
            }

            sleep(1);



        }
        $section3->writeln('Tahám data');

        /*
        $section3->overwrite('DEDNA');
        sleep(1);
        $section3->overwrite('EDNA');
        sleep(1);
        $section3->overwrite('TYČKA');
        sleep(1);
        $section3->overwrite('PIČKA');
        sleep(1);
        $section3->overwrite('Lůj');
        $section3->clear();
        */



        return Command::SUCCESS;
    }
}