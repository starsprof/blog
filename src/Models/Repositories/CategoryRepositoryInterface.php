<?php


namespace App\Models\Repositories;


use App\Models\Category;

interface CategoryRepositoryInterface
{
    /**
     * Find one Category by Id
     * @param int $id
     * @return Category|null
     */
    public function findOneById(int $id): ?Category;

    /**
     * Find all Categories
     * @param int $page
     * @param int $count
     * @return Category[]
     */
    public function findAll(int $page=1, $count=10000): array;

    /**
     * Delete one Category by Id
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool;

    /**
     * Save new Category
     * @param Category $category
     * @return Category
     */
    public function create(Category $category): Category;

    /**
     * Update Category
     * @param Category $category
     * @return bool
     */
    public function update(Category $category): bool;

    /**
     * Get all Categories count
     * @return int
     */
    public function count(): int;
}