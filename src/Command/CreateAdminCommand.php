<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a admin user.',
)]
class CreateAdminCommand extends Command
{
    private SymfonyStyle $io;
    protected EntityManagerInterface $entityManager;
    protected UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $questionEmail = new Question('Email: ');
        $questionEmail->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException(
                    'The email is not valid'
                );
            }

            return $answer;
        });
        $questionPassword = new Question('Password: ');
        $questionPassword->setHidden(true);
        $questionPassword->setHiddenFallback(false);

        $email = $helper->ask($input, $output, $questionEmail);
        $password = $helper->ask($input, $output, $questionPassword);

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success("Admin user created ! -> Email: $email / Password: $password");

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a user...');
    }
}