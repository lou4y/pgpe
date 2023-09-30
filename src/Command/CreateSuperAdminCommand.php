<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CreateSuperAdminCommand extends Command
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager,UserPasswordHasherInterface $userPasswordHasher)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->passwordEncoder = $userPasswordHasher;
    }

    protected function configure()
    {
        $this
            ->setName('app:create-super-admin')
            ->setDescription('Create a super admin user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Create a super admin user
        $superAdmin = new User();
        $superAdmin->setEmail('SuperAdmin@pgpe.com');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN']);
        $superAdmin->setPassword($this->passwordEncoder->hashPassword($superAdmin, 'AHnY6UOpZuVAgkP'));

        $this->entityManager->persist($superAdmin);
        $this->entityManager->flush();

        $output->writeln('Super admin user created.');

        return Command::SUCCESS;
    }
}