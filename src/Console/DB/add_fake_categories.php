<?php

use App\Models\Repositories\CategoryRepositoryInterface;
session_start();
require_once __DIR__.'/../../../vendor/autoload.php';

$path =  dirname(dirname(__DIR__));
putenv("ROOT=$path");

$bootstrap = new \App\Core\Bootstrap();
/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $bootstrap->container->get(CategoryRepositoryInterface::class);

for ($i = 1; $i <=10; $i++) {
    $category = new \App\Models\Category($bootstrap->container);
    $category->setName('Test category -'.$i);
    $category->setDescription('Test category -'.$i.' description');
    $category->setImage('https://fakeimg.pl/350x200/?text=' . $category->getName());
    $categoryRepository->create($category);
}

