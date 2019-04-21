<?php


namespace App\Models\Repositories;


use App\Models\User;
use PDO;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Find one user by id
     * @param int $id
     * @return User|null
     */
    public function findOneById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id=:id LIMIT 1");
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class, [$this->container]);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ? $user : null;
    }

    /**
     * Find many users by ids
     * @param array $ids
     * @return User[]
     */
    public function findManyByIds($ids = array()): array
    {
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM users WHERE id IN ($in)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class, [$this->container]);
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    /**
     * Find all users
     * @return User[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM users');
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class, [$this->container]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete user by id
     * @param $id
     * @return bool
     */
    public function deleteOneById(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id=:id');
        $stmt->execute(array('id' => $id));
        return (bool)$stmt->rowCount();
    }

    /**
     * Create new User
     * @param User $user
     * @return User
     */
    public function create(User $user): User
    {
        $stmt = $this->pdo->prepare('INSERT INTO `users` (`email`, `password`, `name`, `avatar`) 
                            VALUES (:email, :password, :name, :avatar)');
        $stmt->execute([
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'name' => $user->getName(),
            'avatar' => $user->getAvatar()
        ]);
        $id = (integer)$this->pdo->lastInsertId();
        $user = $this->findOneById($id);
        return $user;
    }

    /**
     * Check user
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function checkAuth(string $email, string $password): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email=:email LIMIT 1');
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class, [$this->container]);
        $stmt->execute(['email' => $email]);
        /** @var User $user */
        $user = $stmt->fetch();
        return $user && password_verify($password, $user->getPassword()) ? $user : null;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function checkEmailAvailability(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email=:email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return ! ((bool) $stmt->fetch());
    }

    /**
     * Update User, skip email
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        $stmt = $this->pdo->prepare('UPDATE `users` SET name=:name, avatar=:avatar, password=:password WHERE id=:id');
        $stmt->execute([
            'name' => $user->getName(),
            'avatar' => $user->getAvatar(),
            'password' => $user->getPassword(),
            'id' => $user->getId()
        ]);
        return (bool)$stmt->rowCount();
    }
}