<?php


namespace App\Console\DB;


use App\Models\Category;
use App\Models\Post;
use App\Models\Repositories\CategoryRepositoryInterface;
use App\Models\Repositories\PostRepositoryInterface;
use App\Models\Repositories\TagRepositoryInterface;
use App\Models\Repositories\UserRepositoryInterface;
use App\Models\Tag;
use App\Models\User;
use Faker\Factory;
use Faker\Generator;
use Psr\Container\ContainerInterface;

/**
 * Class FakeEntityGenerator
 * @package App\Console\DB
 */
class FakeEntityGenerator
{
    /**
     * @var Generator
     */
    private $faker;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * FakeEntityGenerator constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->faker = Factory::create();
    }

    /**
     * Get fake Users
     * @param int $count
     * @return User[]
     */
    public function getUsers(int $count): array
    {
        $users = [];
        for ($i = 0; $i < $count; $i++) {
            $user = new User($this->container);
            $user->setEmail($this->faker->unique()->email);
            $user->setName($this->faker->firstName . ' ' . $this->faker->lastName);
            $user->setPassword(password_hash('123123', PASSWORD_DEFAULT));
            $user->setAvatar($this->faker->imageUrl(480, 480, 'people', true));
            $users[] = $user;
        }
        return $users;
    }

    /**
     * Get fake Categories
     * @param int $count
     * @return Category[]
     */
    public function getCategories(int $count): array
    {
        $categories = [];
        for ($i = 0; $i < $count; $i++) {
            $category = new Category($this->container);
            $category->setName('Test category -' . $i);
            $category->setDescription('Test category -' . $i . ' description');
            $category->setImage('https://fakeimg.pl/350x200/?text=' . $category->getName());
            $categories[] = $category;
        }
        return $categories;
    }

    /**
     * Get fake Posts
     * @param int $count
     * @return Post[]
     */
    public function getPosts(int $count): array
    {
        $posts = [];
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->container->get(CategoryRepositoryInterface::class);
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $this->container->get(UserRepositoryInterface::class);
        /** @var TagRepositoryInterface $tagRepository */
        $tagRepository = $this->container->get(TagRepositoryInterface::class);
        $users = $userRepository->findAll();
        $usersIds = array_map(function ($user) {
            return $user->getId();
        }, $users);
        $categories = $categoryRepository->findAll();
        $categoryIds = array_map(function ($category) {
            return $category->getId();
        }, $categories);
        $tags = $tagRepository->findAll();
        $tagsIds = array_map(function ($tag) {
            return $tag->getId();
        }, $tags);

        for ($i = 0; $i < $count; $i++) {
            $post = new Post($this->container);
            $post->setTitle($this->faker->sentence);
            $post->setSlug($this->faker->unique()->slug);
            $post->setDescription($this->faker->text(rand(20, 60)));
            $post->setImage($this->faker->imageUrl($width = 640, $height = 480, null, true));
            $post->setBody($this->getPostBody());
            $post->setPublishedAt($this->faker->dateTime);
            $post->setPublished($this->faker->boolean(90));
            $post->setCategoryId($categoryIds[array_rand($categoryIds)]);
            $post->setAuthorId($usersIds[array_rand($usersIds)]);
            $post->setTagsIds($this->faker->randomElements($tagsIds,rand(0,5)));
            $posts[] = $post;
        }
        return $posts;
    }

    /**
     * Get fake Tags
     * @return Tag[]
     */
    public function getTags(): array
    {
        $tags = [];
        $tagsTitles = ['Advertising', 'Advice', 'Android', 'Anime', 'Apple', 'Architecture', 'Art', 'Baking', 'Beauty', 'Bible', 'Blog', 'Blogging', 'Book Reviews', 'Books', 'Business', 'Canada', 'Cars', 'Cartoons', 'Celebrities', 'Celebrity', 'Children', 'Christian', 'Christianity', 'Comedy', 'Comics', 'Cooking', 'Cosmetics', 'Crafts', 'Cuisine', 'Culinary', 'Culture', 'Dating', 'Design', 'Diy', 'Dogs', 'Drawing', 'Economy', 'Education', 'Entertainment', 'Environment', 'Events', 'Exercise', 'Faith', 'Family', 'Fantasy', 'Fashion', 'Fiction', 'Film', 'Fitness', 'Folk', 'Food', 'Football', 'France', 'Fun', 'Funny', 'Gadgets', 'Games', 'Gaming', 'Geek', 'Google', 'Gossip', 'Graphic Design', 'Green', 'Health', 'Hip', 'History', 'Home', 'Home Improvement', 'Homes', 'Humor', 'Humour', 'Hunting', 'Illustration', 'Indie', 'Inspiration', 'Interior Design', 'Internet', 'Internet Marketing', 'Iphone', 'Italy', 'Kids', 'Landscape', 'Law', 'Leadership', 'Life', 'Lifestyle', 'Literature', 'London', 'Love', 'Management', 'Marketing', 'Media', 'Men', 'Mobile', 'Money', 'Movies', 'Music', 'Nature', 'News', 'Nutrition', 'Opinion', 'Painting', 'Parenting', 'Personal', 'Personal Development', 'Pets', 'Philosophy', 'Photo', 'Photography', 'Photos', 'Pictures', 'Poetry', 'Politics', 'Real Estate', 'Recipes', 'Relationships', 'Religion', 'Retirement', 'Reviews', 'Sales', 'Satire', 'Science', 'Seo', 'Sex', 'Shopping', 'Soccer', 'Social Media', 'Software', 'Spirituality', 'Sports', 'Technology', 'Television', 'Tips', 'Travel', 'Tutorials', 'Tv', 'Uk', 'Vacation', 'Video', 'Videos', 'Voices.com', 'Web', 'Web Design', 'Weight Loss', 'Wellness', 'Wildlife', 'Wine', 'Women', 'Wordpress', 'Writing'];
        $tagsTitles = $this->faker->randomElements($tagsTitles, rand(20,50));
        foreach ($tagsTitles as $title) {
            $tag = new Tag($this->container);
            $tag->setTitle(trim($title));
            $slug = str_replace(' ', '-', trim($title));
            $slug = strtolower($slug);
            $tag->setSlug($slug);
            $tags[] = $tag;
        }
        return $tags;
    }


    /**
     * Get formatted html text
     * @return string
     */
    private function getPostBody()
    {
        $paragraphs = $this->faker->numberBetween(3, 10);
        $text = '';
        for ($i = 0; $i <= $paragraphs; $i++) {
            $text .= '<p>' . $this->faker->text($this->faker->numberBetween(300, 1000)) . '</p>';
        }
        return $text;
    }

}