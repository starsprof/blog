<?php

use App\Models\Post;
use App\Models\Repositories\PostRepositoryInterface;

require_once __DIR__.'/../../../vendor/autoload.php';
session_start();
$path =  dirname(dirname(__DIR__));
putenv("ROOT=$path");

$bootstrap = new \App\Core\Bootstrap();
/** @var PostRepositoryInterface $postRepository */
$postRepository = $bootstrap->container->get(PostRepositoryInterface::class);
/** @var \App\Models\Repositories\CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $bootstrap->container->get(\App\Models\Repositories\CategoryRepositoryInterface::class);

$faker = \Faker\Factory::create();
function getBody(\Faker\Generator $faker)
{
    $paragraphs = $faker->numberBetween(3,10);
    $text ='';
    for($i=0; $i<=$paragraphs;$i++)
    {
        $text .='<p>'.$faker->text($faker->numberBetween(300,1000)).'</p>';
    }
    return $text;
}

$categories = $categoryRepository->findAll();
$categoryIds = array_map(function ($category){
    return $category->getId();
},$categories);

for($i=1; $i<50; $i++) {
    $post = new Post();
    $post->setTitle($faker->sentence);
    $post->setSlug($faker->unique()->slug);
    $post->setDescription($faker->text(rand(300, 700)));
    $post->setImage($faker->imageUrl($width = 640, $height = 480));
    $post->setBody(getBody($faker));
    $post->setCreatedAt($faker->dateTime);
    $post->setUpdatedAt($faker->dateTime);
    $post->setPublishedAt($faker->dateTime);
    $post->setPublished($faker->boolean(90));
    $post->setCategoryId($categoryIds[array_rand($categoryIds)]);
    $postRepository->create($post);
}