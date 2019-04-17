<?php


namespace App\Models\Repositories;


use App\Models\User;
use PDO;

class UserRepository extends BaseRepository
{

    /**
     * Find one user by id
     * @param $id
     * @return User|null
     */
    public function findOneById($id): ?User
    {
          $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id=:id LIMIT 1");
          $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, User::class);
          $stmt->execute(['id' => $id]);
          $user = $stmt->fetch();
          return $user ? $user : null;
    }

    /**
     * Find many users by ids
     * @param array $ids
     * @return User[]
     */
    public function findManyByIds($ids = array()):array
    {
        $in  = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM users WHERE id IN ($in)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, User::class);
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
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, User::class);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete user by id
     * @param $id
     * @return bool
     */
    public function deleteOneById($id):bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id=:id');
        $stmt->execute(array('id' => $id));
        return (bool) $stmt->rowCount();
    }
}