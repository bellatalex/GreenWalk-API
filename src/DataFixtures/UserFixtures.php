<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private const PASSWORD = 'password';
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * Fixture function
     * @param ObjectManager $manager
     * @throws \Doctrine\DBAL\DBALException
     */
    public
    function load(ObjectManager $manager)
    {
        $this->generateAdmin($manager);
        $this->generateUsers($manager);

        $manager->flush();
    }

    // Generate an user who has the role Admin

    private function generateAdmin(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $admin->setEmail('admin@mail.com');
        $admin->setBirthdate($faker->dateTime("2001-01-01"));
        $admin->setFirstName($faker->firstName);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->userPasswordEncoder->encodePassword($admin, self::PASSWORD));

        $manager->persist($admin);
    }

    // Generate users

    private function generateUsers(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->safeEmail);
            $user->setBirthdate($faker->dateTime("now"));
            $user->setFirstName($faker->firstName);
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, self::PASSWORD));

            $manager->persist($user);
        }
    }


}
