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
        $category = $stmt->fetch();
        return $category ? $category : null;
    }

    /**
     * Find all Categories
     * @param int $page
     * @param int $count
     * @return Category[]
     */
    public function findAll(int $page=1, $count=10000): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories LIMIT :limit OFFSET :offset');
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Category::class, [$this->container]);
        $stmt->execute([
            'limit' => $count,
            'offset' => (($page-1)*$count)
        ]);
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

    /**
     * Get all Categories count
     * @return int
     */
    public function count(): int
    {
        return $this->pdo->query('SELECT count(*) FROM categories')->fetchColumn();
    }

    /**
     * Check if name dont exists in DB
     * @param string $name
     * @return bool
     */
    public function checkNameAvailability(string $name): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE name=:name');
        $stmt->execute(['name' => $name]);
        return !(bool)$stmt->rowCount();
    }
}