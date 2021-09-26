<?php

declare(strict_types=1);

namespace App\Command;

use App\DataProvider\UserDataProvider;
use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    /**
     * @param UserManager $userManager
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param string|null $name
     */
    public function __construct(
        UserManager $userManager,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        string $name = null
    ) {
        parent::__construct($name);
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure(): void
    {
        $this->setName('app:create-admin');
        $definition = [
            new InputArgument('firstName', InputArgument::REQUIRED, 'First name'),
            new InputArgument('lastName', InputArgument::REQUIRED, 'Last name'),
            new InputArgument('patronymic', InputArgument::REQUIRED, 'Patronymic'),
            new InputArgument('email', InputArgument::REQUIRED, 'E-mail'),
            new InputArgument('phone', InputArgument::REQUIRED, 'Phone number'),
            new InputArgument('birthday', InputArgument::REQUIRED, 'Birthday (yyyy-mm-dd)'),
            new InputArgument('gender', InputArgument::REQUIRED, 'Gender (male/female)'),
            new InputArgument('password', InputArgument::REQUIRED, 'Password'),
        ];
        $this->setDefinition($definition);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Create Admin');

        if (!$input->getArgument('firstName')) {
            do {
                $firstName = $io->ask('First name');
                if (empty($firstName)) {
                    $io->error('First name not be empty');
                }
            } while (empty($firstName));
            $input->setArgument('firstName', $firstName);
        }
        if (!$input->getArgument('lastName')) {
            do {
                $lastName = $io->ask('Last name');
                if (empty($lastName)) {
                    $io->error('Last name not be empty');
                }
            } while (empty($lastName));
            $input->setArgument('lastName', $lastName);
        }
        if (!$input->getArgument('email')) {
            do {
                $email = $io->ask('Email');
                if (empty($email)) {
                    $io->error('Email not be empty');
                }
            } while (empty($email));
            $input->setArgument('email', $email);
        }
        if (!$input->getArgument('phone')) {
            do {
                $phone = $io->ask('Phone');
                if (empty($phone)) {
                    $io->error('Phone not be empty');
                }
            } while (empty($phone));
            $input->setArgument('phone', $phone);
        }
        if (!$input->getArgument('phone')) {
            do {
                $phone = $io->ask('Phone');
                if (empty($phone)) {
                    $io->error('Phone not be empty');
                }
            } while (empty($phone));
            $input->setArgument('phone', $phone);
        }
        if (!$input->getArgument('birthday')) {
            do {
                $birthday = $io->ask('birthday');
                if (empty($birthday)) {
                    $io->error('Birthday not be empty');
                }
            } while (empty($birthday));
            $input->setArgument('birthday', $birthday);
        }
        if (!$input->getArgument('patronymic')) {

            $patronymic = $io->ask('Patronymic');

            $input->setArgument('patronymic', $patronymic);
        }
        if (!$input->getArgument('password')) {
            do {
                $password = $io->ask('Password');
                if (empty($password)) {
                    $io->error('Password not be empty');
                }
            } while (empty($password));
            $input->setArgument('password', $password);
        }
        if (!$input->getArgument('gender')) {
            do {
                $gender = $io->ask('gender');
                if (empty($gender)) {
                    $io->error('Gender not be empty');
                }
            } while (empty($gender));
            $input->setArgument('gender', $gender);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');
        $email = $input->getArgument('email');
        $phone = $input->getArgument('phone');
        $birthday = $input->getArgument('birthday');
        $gender = $input->getArgument('gender');
        $patronymic = $input->getArgument('patronymic');
        $password = $input->getArgument('password');
        try {
            $existAdminEmail = $this->userRepository->findOneBy(['email' => $email]);
            if ($existAdminEmail instanceof User) {
                $io->info('Пользователь с таким email уже существует');
            }
            $existAdminPhone = $this->userRepository->findOneBy(['phone' => $phone]);
            if ($existAdminPhone instanceof User) {
                $io->info('Пользователь с таким номером телефона уже существует');
            }

            $user = $this->userManager->create(
                $firstName,
                $lastName,
                [UserDataProvider::ROLE_ADMIN],
                $email,
                $phone,
                \DateTime::createFromFormat('Y-m-d', $birthday),
                $gender,
                $patronymic,
                null
            );
            $encoded = $this->passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encoded);
            $this->entityManager->flush();

            $io->success('Администратор создан');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Ошибка создания администратора: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}