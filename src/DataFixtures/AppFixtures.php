<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\BookList;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        //Users
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');

            $users[] = $user;
            $manager->persist($user);

        }

        //Books
        $books = [];
        for ($i = 0; $i < 50; $i++){$book = new Book();
        $book->setName($this->faker->words(3, true))
            ->setAuthor($this->faker->words(2, true))
            ->setUser($users[mt_rand(0, count($users) - 1)]);

        $books[] = $book;
        $manager->persist($book);
        }

        //Booklists
        for ($i=0; $i < 15; $i++) {
            $booklist = new BookList;
            $booklist->setName($this->faker->words(2, true))
                ->setDescription($this->faker->text(50))
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setUser($users[mt_rand(0, count($users) - 1)]);

            for ($k=0; $k < mt_rand(3, 5); $k++) {
                $booklist->addBook($books[mt_rand(0, count($books) - 1)]);
            }


            $booklists[] = $booklist;
            $manager->persist($booklist);
        }

        $manager->flush();
    }
}
