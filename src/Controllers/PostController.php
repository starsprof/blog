<?php


namespace App\Controllers;


use App\Models\Repositories\PostRepositoryInterface;
use Psr\Container\ContainerInterface;

class PostController extends BaseController
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->postRepository = $this->container->get(PostRepositoryInterface::class);
    }

    public function adminIndex()
    {

    }
    public function index()
    {

    }
}