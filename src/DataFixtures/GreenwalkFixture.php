<?php

namespace App\DataFixtures;

use App\Entity\Greenwalk;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class GreenwalkFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {

        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $users = $this->userRepository->findAll();

        for ($i = 0; $i < 5; $i++) {
            $greenwalk = new Greenwalk();

            $greenwalk->setName($faker->name);
            $greenwalk->setLatitude(rand(48726829, 49272555) / 1000000);
            $greenwalk->setLongitude(rand(1886397, 3142969) / 1000000);
            $greenwalk->setCity($faker->city);
            $greenwalk->setDescription($faker->sentence(100));
            $greenwalk->setZipCode($faker->postcode);
            $greenwalk->setAuthor($faker->randomElement($users));
            $greenwalk->setStreet($faker->streetName);
            $greenwalk->setState($faker->boolean(60));
            $greenwalk->setDatetime($faker->dateTimeBetween('now', '+2months'));

            for($y=0;$y < 2;$y++){
                $greenwalk->addParticipant($faker->randomElement($users));
            }

            $manager->persist($greenwalk);
        }

            $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
