<?php


namespace App\Controllers;


use App\Models\Post;
use App\Models\Repositories\CategoryRepositoryInterface;
use App\Models\Repositories\PostRepositoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class PostController extends BaseController
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /** @var int  */
    private const PER_PAGE_COUNT = 5;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->postRepository = $this->container->get(PostRepositoryInterface::class);
        $this->categoryRepository = $this->container->get(CategoryRepositoryInterface::class);
    }

    public function adminIndex(Request $request, Response $response)
    {
        $page = $request->getAttribute('page');
        if(empty($page))
        {
            $page = 1;
        }
        $total = $this->postRepository->count();
        $pages = ceil($total/self::PER_PAGE_COUNT);
        $posts = $this->postRepository->findPage($page, self::PER_PAGE_COUNT);
        return $this->view->render($response,'posts/adminIndex.twig', ['posts' => $posts, 'page' => $page, 'pages' => $pages, 'total' => $total]);
    }

    public function adminView(Request $request, Response $response)
    {
        $id = (int) $request->getAttribute('id');
        $post = $this->postRepository->findOneById($id);
        if(empty($post))
        {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        return $this->view->render($response, 'posts/adminView.twig', ['post' => $post]);
    }
    public function index()
    {
    }

    public function add(Request $request, Response $response)
    {
        if($request->isGet())
        {
            $categories = $this->postRepository->getCategoriesKeysPairs();
            return $this->view->render($response, 'posts/add.twig', ['categories' => $categories]);
        }
        $params = $request->getParsedBody();
        $post = new Post($this->container);
        $post->setSlug($params['inputSlug']);
        $post->setTitle($params['inputTitle']);
        $post->setDescription($params['inputDescription']);
        $errors = Post::validate($post);
        var_dump($errors);
    }
}