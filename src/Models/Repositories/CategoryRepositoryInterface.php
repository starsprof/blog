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
     * @return Category[]
     */
    public function findAll(): array;

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
}