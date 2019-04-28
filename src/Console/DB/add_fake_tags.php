<?php

require_once __DIR__.'/../../../vendor/autoload.php';
session_start();
$path =  dirname(dirname(__DIR__));
putenv("ROOT=$path");

$bootstrap = new \App\Core\Bootstrap();
/** @var \App\Models\Repositories\TagRepositoryInterface $tagRepository */
$tagRepository = $bootstrap->container->get(\App\Models\Repositories\TagRepositoryInterface::class);

$tagsTitles = ['Advertising', 'Advice', 'Android', 'Anime', 'Apple', 'Architecture', 'Art', 'Baking', 'Beauty', 'Bible', 'Blog', 'Blogging', 'Book Reviews', 'Books', 'Business', 'Canada', 'Cars', 'Cartoons', 'Celebrities', 'Celebrity', 'Children', 'Christian', 'Christianity', 'Comedy', 'Comics', 'Cooking', 'Cosmetics', 'Crafts', 'Cuisine', 'Culinary', 'Culture', 'Dating', 'Design', 'Diy', 'Dogs', 'Drawing', 'Economy', 'Education', 'Entertainment', 'Environment', 'Events', 'Exercise', 'Faith', 'Family', 'Fantasy', 'Fashion', 'Fiction', 'Film', 'Fitness', 'Folk', 'Food', 'Football', 'France', 'Fun', 'Funny', 'Gadgets', 'Games', 'Gaming', 'Geek', 'Google', 'Gossip', 'Graphic Design', 'Green', 'Health', 'Hip', 'History', 'Home', 'Home Improvement', 'Homes', 'Humor', 'Humour', 'Hunting', 'Illustration', 'Indie', 'Inspiration', 'Interior Design', 'Internet', 'Internet Marketing', 'Iphone', 'Italy', 'Kids', 'Landscape', 'Law', 'Leadership', 'Life', 'Lifestyle', 'Literature', 'London', 'Love', 'Management', 'Marketing', 'Media', 'Men', 'Mobile', 'Money', 'Movies', 'Music', 'Nature', 'News', 'Nutrition', 'Opinion', 'Painting', 'Parenting', 'Personal', 'Personal Development', 'Pets', 'Philosophy', 'Photo', 'Photography', 'Photos', 'Pictures', 'Poetry', 'Politics', 'Real Estate', 'Recipes', 'Relationships', 'Religion', 'Retirement', 'Reviews', 'Sales', 'Satire', 'Science', 'Seo', 'Sex', 'Shopping', 'Soccer', 'Social Media', 'Software', 'Spirituality', 'Sports', 'Technology', 'Television', 'Tips', 'Travel', 'Tutorials', 'Tv', 'Uk', 'Vacation', 'Video', 'Videos', 'Voices.com', 'Web', 'Web Design', 'Weight Loss', 'Wellness', 'Wildlife', 'Wine', 'Women', 'Wordpress', 'Writing'];
foreach ($tagsTitles as $title)
{
    $tag = new \App\Models\Tag($bootstrap->container);
    $tag->setTitle(trim($title));
    $slug = str_replace(' ', '-', trim($title));
    $slug= strtolower($slug);
    $tag->setSlug($slug);
    $tagRepository->create($tag);
}
