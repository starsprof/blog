<?php


namespace App\Models\Repositories;


use App\Models\Category;
use PDO;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{

    /**
     * Find one Category by Id
     * @param int $id
     * @return Category|null
     */
    public function findOneById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id=:id LIMIT 1");
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Category::class, [$this->container]);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ? $user : null;
    }

    /**
     * Find all Categories
     * @return Category[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories');
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Category::class, [$this->container]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete one Category by Id
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id=:id');
        $stmt->execute(array('id' => $id));
        return (bool)$stmt->rowCount();
    }

    /**
     * Save new Category
     * @param Category $category
     * @return Category
     */
    public function create(Category $category): Category
    {
        $stmt = $this->pdo->prepare('INSERT INTO categories (`name`, `image`, `description`)
                                    VALUES (:name, :image, :description)');
        $stmt->execute([
            'name' => $category->getName(),
            'image' => $category->getImage(),
            'description' => $category->getDescription(),
        ]);
        $id = (integer)$this->pdo->lastInsertId();
        $category = $this->findOneById($id);
        return $category;
    }

    /**
     * Update Category
     * @param Category $category
     * @return bool
     */
    public function update(Category $category): bool
    {
        $stmt = $this->pdo->prepare('UPDATE categories SET name=:name, image=:image, description=:description WHERE id=:id');
        $stmt->execute([
            'name' => $category->getName(),
            'image' => $category->getImage(),
            'description' => $category->getDescription(),
            'id' => $category->getId()
        ]);
        return (bool)$stmt->rowCount();
    }
}