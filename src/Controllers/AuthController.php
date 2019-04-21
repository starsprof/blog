<?php

namespace App\Controllers;

use App\Models\Auth;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthController extends BaseController
{
    /**
     * @var Auth
     */
    protected $auth;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->auth = $this->container->get(Auth::class);
    }

    public function signIn(Request $request, Response $response)
    {
        if($request->isGet())
        {
            $this->view->render($response, 'auth/signin.twig');
        }
        elseif ($request->isPost())
        {
            $params = $request->getParsedBody();
            $email = $params['inputEmail'];
            $password = $params['inputPassword'];
            $user = $this->auth->signIn($email, $password);
            if($user)
            {
                $this->auth->signInByUser($user);
                return $response->withRedirect('/');
            }
            else
            {
                return $this->view->render($response, 'auth/signin.twig',
                    ['email' => $email, 'error' => 'Incorrect email or password']);

            }
        }
    }

    public function signUp(Request $request, Response $response)
    {
        if ($request->isGet()) {
            $this->view->render($response, 'auth/signup.twig');
        }
        elseif ($request->isPost())
        {
            $params = $request->getParsedBody();
            $email = $params['inputEmail'];
            $password = $params['inputPassword'];
            $repeatPassword = $params['inputRepeatPassword'];
            $errors = $this->auth->validateSignUp($email, $password, $repeatPassword);
            if(empty($errors))
            {
                $user = $this->auth->signUp($email, $password);
                $this->auth->signInByUser($user);
                return $response->withRedirect('/');
            }else
            {
                return $this->view->render($response, 'auth/signup.twig', [
                    'errors' => $errors,
                    'email' => $email,
                    'password' => $password,
                    'repeatPassword' => $repeatPassword
                ]);
            }
        }
    }

    public function signOut(Request $request, Response $response)
    {
        $this->auth->signOut();
        return $response->withRedirect('/');
    }
}