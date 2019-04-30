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

    public function index(Request $request, Response $response)
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

    public function remove(Request $request, Response $response)
    {
        $id = (int) $request->getParsedBody()['id'];
        $this->categoryRepository->deleteOneById($id);
        $this->flash->addMessage(self::MESSAGE_WARNING, 'Category successfully deleted');
        return $response->withRedirect('/admin/categories');
    }

    public function add(Request $request, Response $response)
    {
        if($request->isGet())
        {
            return $this->view->render($response, 'categories/add.twig');
        }

        $params = $request->getParsedBody();
        $name = trim($params['inputName']);
        $description = trim($params['inputDescription']);

        $nameValidationErrors = Category::validateName($name);
        $descriptionValidationErrors = Category::validateDescription($description);
        $errors = array_merge($nameValidationErrors, $descriptionValidationErrors);

        $category = new Category($this->container);
        $category->setName($name);
        $category->setDescription($description);

        if(!empty($errors))
        {
            return $this->view->render($response, 'categories/add.twig', ['category' => $category, 'errors' => $errors]);
        }

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
        $this->flash->addMessage(self::MESSAGE_INFO, 'Category successfully added');
        return $response->withRedirect('/admin/categories');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Slim\Exception\NotFoundException
     */
    public function getEdit(Request $request, Response $response)
    {
        $id = (int) $request->getAttribute('id');
        $category = $this->categoryRepository->findOneById($id);
        if(empty($category))
        {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        return $this->view->render($response, 'categories/edit.twig', ['category' => $category]);
    }

    public function postEdit(Request $request, Response $response)
    {
        $needUpdate = false;
        $params = $request->getParsedBody();
        $newName = trim($params['inputName']);
        $newDescription = trim($params['inputDescription']);

        $nameValidationErrors = Category::validateName($newName);
        $descriptionValidationErrors = Category::validateDescription($newDescription);
        $errors = array_merge($nameValidationErrors, $descriptionValidationErrors);

        $category = $this->categoryRepository->findOneById($params['inputId']);
        if(!empty($errors))
        {
            $category->setName($newName);
            $category->setDescription(($newDescription));
            return $this->view->render($response, 'categories/edit.twig', ['category' => $category, 'errors' => $errors]);
        }

        if($category->getName()!=$newName)
        {
            if($this->categoryRepository->checkNameAvailability($newName))
            {
                $needUpdate = true;
                $category->setName($newName);
            }else{
                $errors[] = 'Category Name already in use';
                $category->setName($newName);
                $category->setDescription($newDescription);
                return $this->view->render($response, 'categories/edit.twig', ['category' => $category, 'errors' => $errors]);
            }
        }

        if($category->getDescription()!=$newDescription)
        {
            $needUpdate = true;
            $category->setDescription($newDescription);
        }

        /** * @var UploadedFile[] $uploadedFiles */
        $uploadedFiles = $request->getUploadedFiles();
        if (!empty($uploadedFiles['inputImage'])) {
            $needUpdate = true;
            $directory = getenv('ROOT').'/public/'.getenv('UPLOAD_DIR');
            $uploadedFile = $uploadedFiles['inputImage'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $filename = $this->moveUploadedFile($directory, $uploadedFile);
                $category->setImage('/'.getenv('UPLOAD_DIR').'/'.$filename);
            }
        }
        if($needUpdate)
        {
            $this->categoryRepository->update($category);
        }
        $this->flash->addMessage(self::MESSAGE_INFO, 'Category successfully saved');
        return $response->withRedirect('/admin/categories');
    }

}