<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('en_EN');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++){$book = new Book();
        $book->setName($this->faker->words(3, true))
            ->setAuthor($this->faker->words(2, true));

        $manager->persist($book);
        }

        $manager->flush();
    }
}
