<?php

namespace App\Controllers;

use App\Models\Repositories\TagRepositoryInterface;
use App\Models\Tag;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class TagController extends BaseController
{
    private const PER_PAGE_COUNT = 10;
    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->tagRepository = $this->container->get(TagRepositoryInterface::class);

    }

    public function adminIndex(Request $request, Response $response)
    {
        $page = $request->getAttribute('page');
        if (empty($page)) {
            $page = 1;
        }
        $total = $this->tagRepository->count();
        $pages = ceil($total / self::PER_PAGE_COUNT);
        $tags = $this->tagRepository->findPage($page, self::PER_PAGE_COUNT);
        return $this->view->render(
            $response,
            'tags/adminIndex.twig',
            ['tags' => $tags, 'page' => $page, 'pages' => $pages, 'total' => $total]
        );
    }

    public function delete(Request $request, Response $response)
    {
        $id = (int)$request->getParsedBody()['id'];
        $this->tagRepository->deleteOneById($id);
        $this->flash->addMessage(self::MESSAGE_WARNING, 'Tag successfully deleted');
        return $response->withRedirect('/admin/tags');
    }

    public function ajaxEdit(Request $request, Response $response)
    {

    }

    public function edit(Request $request, Response $response)
    {
        if($request->isGet()){
            $id = $request->getAttribute('id');
            $tag = $this->tagRepository->findOneById($id);
            return $this->view->render($response, 'tags/edit.twig', ['tag' => $tag]);
        }
        $params = $request->getParsedBody();
        $title = trim($params['title']);
        $slug = trim($params['slug']);
        $tag = $this->tagRepository->findOneById($params['id']);
        $errors = [];
        if ($tag->getTitle() != $title) {
            if (!$this->tagRepository->checkTitleAvailability($title)) {
                $errors['title'] = 'Title already used';
            } else {
                $tag->setTitle($title);
            }
        }
        if ($tag->getSlug() != $slug) {
            if ($this->tagRepository->checkSlugAvailability($slug)) {
                $tag->setSlug($slug);
            } else {
                $errors['slug'] = 'Slug already used';
            }
        }
        $errors = array_merge($errors, Tag::validate($tag));
        if (empty($errors)) {
            $this->tagRepository->update($tag);
            $this->flash->addMessage(self::MESSAGE_INFO, "Tag '$title' updated");
            return $response->withJson(['status' => 'success']);
        }
        return $response->withJson(['status' => 'failed', 'errors' => $errors]);
    }

    public function add(Request $request, Response $response)
    {
        if($request->isGet()){
            return $this->view->render($response, 'tags/add.twig');
        }
        $params = $request->getParsedBody();
        $title = trim($params['title']);
        $slug = trim($params['slug']);
        $tag = new Tag($this->container);
        $tag->setSlug($slug);
        $tag->setTitle($title);
        $errors = Tag::validate($tag);
        if(!$this->tagRepository->checkTitleAvailability($title)){
            $errors['title'] = 'Title already used';
        }
        if(!$this->tagRepository->checkSlugAvailability($slug)){
            $errors['slug'] = 'Slug already used';
        }
        if(empty($errors))
        {
            $this->tagRepository->create($tag);
            $this->flash->addMessage(self::MESSAGE_INFO, "Tag '$title' added'");
            return $response->withJson(['status' => 'success']);
        }
        else{
            return $response->withJson(['status' => 'failed', 'errors' => $errors]);
        }
    }
}