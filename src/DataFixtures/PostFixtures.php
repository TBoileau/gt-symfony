<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private string $uploadDir)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /** @var array<array-key, Category> $categories */
        $categories = $manager->getRepository(Category::class)->findAll();

        /** @var array<array-key, User> $users */
        $users = $manager->getRepository(User::class)->findAll();

        /** @var array<array-key, Tag> $tags */
        $tags = $manager->getRepository(Tag::class)->findAll();

        foreach ($users as $user) {
            foreach ($categories as $category) {
                for ($i = 1; $i <= 5; ++$i) {
                    shuffle($tags);
                    $post = new Post();
                    $post->setCategory($category);
                    $post->setUser($user);
                    $post->setPublishedAt(new DateTimeImmutable());
                    $post->setTitle($faker->words(3, true));
                    $post->setContent($faker->paragraphs(2, true));
                    $post->setImage($faker->image($this->uploadDir, 640, 480, null, false));

                    foreach (array_slice($tags, 0, 2) as $tag) {
                        $post->getTags()->add($tag);
                    }

                    $manager->persist($post);
                }

                $manager->flush();
            }
        }

    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            TagFixtures::class,
            UserFixtures::class
        ];
    }
}
