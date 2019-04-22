<?php


namespace App\Controllers;


use App\Models\Category;
use App\Models\Repositories\CategoryRepositoryInterface;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

/**
 * Class CategoryController
 * @package App\Controllers
 */
class CategoryController extends BaseController
{
    private const COUNT_PER_PAGE = 3;
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * CategoryController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->categoryRepository = $this->container->get(CategoryRepositoryInterface::class);
    }


    public function adminIndex(Request $request, Response $response)
    {
        $page = (int) $request->getAttribute('page');
        $page = $page ? $page : 1;
        $allCount = $this->categoryRepository->count();
        $pages = ceil($allCount / self::COUNT_PER_PAGE);
        if($page > $pages)
        {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        $categories = $this->categoryRepository->findAll($page, self::COUNT_PER_PAGE);
        return $this->view->render($response, 'categories/index.twig',
            [
                'categories' => $categories,
                'pages' => $pages,
                'page' => $page,
                'total' => $allCount]);
    }

    public function adminRemove(Request $request, Response $response)
    {
        $id = (int) $request->getParsedBody()['id'];
        $this->categoryRepository->deleteOneById($id);
        return $response->withRedirect('/admin/categories');
    }

    public function adminAdd(Request $request, Response $response)
    {
        if($request->isGet())
        {
            return $this->view->render($response, 'categories/add.twig');
        }

        //var_dump($directory);
        $category = new Category($this->container);
        $category->setName('test');
        $category->setDescription('test des');

        /** * @var UploadedFile[] $uploadedFiles */
        $uploadedFiles = $request->getUploadedFiles();
        if (!empty($uploadedFiles['inputImage'])) {
            $directory = getenv('ROOT').'/public/'.getenv('UPLOAD_DIR');
            $uploadedFile = $uploadedFiles['inputImage'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($directory, $uploadedFile);
                $category->setImage('/'.getenv('UPLOAD_DIR').'/'.$filename);
            }
        }
        $this->categoryRepository->create($category);
        return $response->withRedirect('/admin/categories');
    }
}