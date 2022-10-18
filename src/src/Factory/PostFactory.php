<?php

namespace App\Factory;

use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Post>
 *
 * @method static Post|Proxy createOne(array $attributes = [])
 * @method static Post[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Post[]|Proxy[] createSequence(array|callable $sequence)
 * @method static Post|Proxy find(object|array|mixed $criteria)
 * @method static Post|Proxy findOrCreate(array $attributes)
 * @method static Post|Proxy first(string $sortedField = 'id')
 * @method static Post|Proxy last(string $sortedField = 'id')
 * @method static Post|Proxy random(array $attributes = [])
 * @method static Post|Proxy randomOrCreate(array $attributes = [])
 * @method static Post[]|Proxy[] all()
 * @method static Post[]|Proxy[] findBy(array $attributes)
 * @method static Post[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Post[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PostRepository|RepositoryProxy repository()
 * @method Post|Proxy create(array|callable $attributes = [])
 */
final class PostFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            // titre avec 75 caractères max
            'title' => self::faker()->sentence(5, true),
            'description' => self::faker()->paragraph,
            'price' => self::faker()->randomFloat(2, 0, 1000),
            'created_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'modified_at' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'user' => UserFactory::random(),
            // on prends 1 à 4 images
            'images' => self::faker()->randomElements(
                [
                    'chat1.jpg',
                    'chat2.jpg',
                    'chat3.jpg',
                    'chat4.jpg',
                ],
                self::faker()->numberBetween(1, 4)
            ),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            ->afterInstantiate(function (Post $post): void {
                $tag = TagFactory::random();
                $tag->addPost($post);
            });
    }

    protected static function getClass(): string
    {
        return Post::class;
    }
}
