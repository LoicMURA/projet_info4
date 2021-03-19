<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\{Category, Comment, Product, User};
use App\Repository\{ProductRepository, UserRepository, CommentRepository};
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $userRepository;
    private $productRepository;
    private $commentRepository;

    public function __construct(UserPasswordEncoderInterface $encoder, UserRepository $userRepo,
                                ProductRepository $productRepo, CommentRepository $commentRepo)
    {
        $this->encoder = $encoder;
        $this->userRepository = $userRepo;
        $this->productRepository = $productRepo;
        $this->commentRepository = $commentRepo;

    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 5; $i++) {
            $user = new user();

            $role = $i === 0 ? 'ROLE_ADMIN' : 'ROLE_USER';
            $password = $this->encoder->encodePassword($user, 'password');

            $user->setUsername($faker->name)
                 ->setEmail($faker->email)
                 ->setPassword($password)
                 ->setRole($role);

            $manager->persist($user);
        }

        for ($i = 1; $i <= 8; $i++) {
            $category = new Category();

            $category->setName($faker->sentence)
                     ->setDescription(implode("\n", $faker->paragraphs))
                     ->setPicture('https://picsum.photos/300/200?random='.$i);

            $manager->persist($category);

            for ($j = 1; $j <= mt_rand(2, 7); $j++) {
                $product = new Product();

                $product->setPicture('https://picsum.photos/300/200?random='.($i*$j + 8))
                        ->setName($faker->sentence)
                        ->setDescription(implode("\n", $faker->paragraphs))
                        ->setPrice($faker->numberBetween(5, 150))
                        ->setCategory($category);

                $manager->persist($product);
            }
        }
        $manager->flush();

        $users = $this->userRepository->findAll();
        $products = $this->productRepository->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < mt_rand(3, 5); $i++) {
                $comment = new Comment();

                $comment->setTitle($faker->sentence)
                    ->setContent(implode("\n", $faker->paragraphs(mt_rand(1, 3))))
                    ->setUser($user)
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setProduct($faker->randomElement($products));

                if (mt_rand(0, 100) > 80) {
                    $now = new \DateTime();
                    $days = $now->diff($comment->getCreatedAt())->days;

                    $comment->setModifiedAt($faker->dateTimeBetween('-'.$days.' days'));
                }

                if (mt_rand(0, 100) >= 90) {
                    $comments = $this->commentRepository->findAll();
                    if (count($comments) > 0) {
                        $comment->setParent($faker->randomElement($comments));
                    }
                }

                $manager->persist($comment);
                $manager->flush();
            }
        }

        $manager->flush();
    }
}
