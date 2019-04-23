<?php


namespace App\Models\Repositories;


use App\Models\Category;
use App\Models\Post;

interface PostRepositoryInterface
{
    /**
     * Find one Post by Id
     * @param int $id
     * @return Post|null
     */
    public function findOneById(int $id): ?Post;

    /**
     * Find all Posts
     * @return Post[]
     */
    public function findAll(): array;

    /**
     * Delete one Post by Id
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool;

    /**
     * Save new Post
     * @param Post $post
     * @return Post
     */
    public function create(Post $post): Post;

    /**
     * Update Post
     * @param Post $post
     * @return bool
     */
    public function update(Post $post): bool;


}