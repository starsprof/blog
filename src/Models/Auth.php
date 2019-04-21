<?php


namespace App\Models;


use App\Models\Repositories\UserRepositoryInterface;
use Psr\Container\ContainerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class Auth extends BaseModel
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * Auth constructor.
     * @param ContainerInterface $container
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(ContainerInterface $container, UserRepositoryInterface $userRepository)
    {
        parent::__construct($container);
        $this->userRepository = $userRepository;
    }

    /**
     * Reload and relogin user from DB
     * @param User $user
     * @return User
     */
    public function reloadUser(User $user): User
    {
        $updatedUser = $this->userRepository->findOneById($user->getId());
        $this->signInByUser($updatedUser);
        return $updatedUser;
    }

    /**
     * Check email availability
     * @param string $email
     * @return bool
     */
    public function checkEmailAvailability(string $email): bool
    {
        return $this->userRepository->checkEmailAvailability($email);
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     */
    public function signUp(string $email, string $password): User
    {
        $user = $this->container->get(User::class);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user = $this->userRepository->create($user);
        return $user;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function signIn(string $email, string $password): ?User
    {
       return $this->userRepository->checkAuth($email, $password);
    }

    /**
     * Sign Out user
     */
    public function signOut()
    {
        unset($_SESSION['user']);
    }

    /**
     * Get auth User or null
     * @return User|null
     */
    public function user() : ?User
    {
        return isset($_SESSION['user']) ? $_SESSION['user'] : null;
    }

    /**
     * Check is user auth
     * @return bool
     */
    public function isAuth() : bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Check is user guest
     * @return bool
     */
    public function isGuest():bool
    {
        return !$this->isAuth();
    }

    /**
     * Sign In user
     * @param User $user
     */
    public function signInByUser(User $user)
    {
        $_SESSION['user'] = $user;
    }

    /**
     * @param string $email
     * @param string $password
     * @param string $repeatPassword
     * @return array
     */
    public function validateSignUp(string $email, string $password, string $repeatPassword): array
    {
        $errors = [];

        try {
            v::email()
                ->setName('Email')
                ->assert($email);
        } catch (NestedValidationException $exception) {
            $errors[] = $exception->getFullMessage();
        } finally {
            try {
                v::noWhitespace()
                    ->length(6, 15)
                    ->setName('password')
                    ->assert($password);
            } catch (NestedValidationException $exception) {
                $errors[] = $exception->getFullMessage();
            }
        }
        if (!$this->checkEmailAvailability($email)) {
            $errors[] = '- email already in use';
        }
        if($password != $repeatPassword)
        {
            $errors[] = '- passwords do not match';
        }
        return $errors;
    }

}