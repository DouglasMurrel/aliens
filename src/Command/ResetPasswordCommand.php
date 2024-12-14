<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of resetPasswordCommand
 *
 * @author murrel
 */
#[AsCommand(name: 'make:my-reset-password')]
class ResetPasswordCommand extends Command {
    protected EntityManagerInterface $em;
    protected UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher) 
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('email',InputArgument::REQUIRED)
            ->addArgument('password',InputArgument::REQUIRED)
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        
        $user = $this->em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if (!$user) {
            $output->writeln('Пользователь не найден');
            return Command::FAILURE;
        }
        
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        
        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
