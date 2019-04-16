<?php


namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class PageController extends BaseController
{
    public function home(Request $request, Response $response)
    {
        return $this->view->render($response, 'pages/home.twig');
    }
}