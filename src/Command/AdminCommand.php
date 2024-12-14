<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of Admin
 *
 * @author murrel
 */
#[AsCommand(name: 'make:admin')]
class AdminCommand extends Command
{
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) 
    {
        $this->em = $em;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('email',InputArgument::REQUIRED)
            ->addOption('unset')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $unset = $input->getOption('unset');
        
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if (!$user) {
            $output->writeln('Пользователь не найден');
            return Command::FAILURE;
        }
        if ($unset) {
            $user->removeRole('ROLE_ADMIN');
        } else {
            $user->addRole('ROLE_ADMIN');
        }
        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
