
PHP script for creating tables and insert fake data:
<?php

if($argc != 2 || in_array($argv[1], array('--help', '-help', '-h', '-?')))
{
?>

This is a command line PHP script with one option.

    Use:
    php <?php echo $argv[0]; ?> <option>
    <option> can be some word you would like
        to print out. With the --help, -help, -h,
        or -? options, you can get this help.

    -a      create tables and add entities
    -t      create all tables
    -f      add all fake entities
<?php
    die();
}


use App\Console\DB\ConsoleBootstrap;
use App\Console\DB\FakeEntityGenerator;
use App\Console\DB\Table;
use App\Models\Repositories\CategoryRepositoryInterface;
use App\Models\Repositories\PostRepositoryInterface;
use App\Models\Repositories\TagRepositoryInterface;
use App\Models\Repositories\UserRepositoryInterface;

require_once __DIR__ . '/../../vendor/autoload.php';

$bootstrap = new ConsoleBootstrap();
$fakeEntityGenerator = new FakeEntityGenerator($bootstrap->container);

$table = new Table($bootstrap->container);

$interactiveCreate = true;
$interactiveInsert = true;

if(in_array('-a', $argv))
{
    $interactiveCreate = false;
    $interactiveInsert = false;
}

if(in_array('-t', $argv))
{
    $interactiveCreate = false;
}

if(in_array('-f', $argv))
{
    $interactiveInsert = false;
}


$table->createTable('users', $interactiveCreate);
$table->addFakeEntity(
    'Users',
    $fakeEntityGenerator->getUsers(10),
    UserRepositoryInterface::class,
    $interactiveInsert
);

$table->createTable('categories', $interactiveCreate);
$table->addFakeEntity(
    'Categories',
    $fakeEntityGenerator->getCategories(10),
    CategoryRepositoryInterface::class,
    $interactiveInsert
);

$table->createTable('tags', $interactiveCreate);
$table->addFakeEntity(
    'Tags',
    $fakeEntityGenerator->getTags(),
    TagRepositoryInterface::class,
    $interactiveInsert
);

$table->createTable('posts_tags', $interactiveCreate);


$table->createTable('posts', $interactiveCreate);
$table->addFakeEntity(
    'Posts',
    $fakeEntityGenerator->getPosts(100),
    PostRepositoryInterface::class,
    $interactiveInsert
);

echo "\n";