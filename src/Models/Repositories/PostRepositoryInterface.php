<?php


namespace App\Models\Repositories;


use App\Models\Post;
use App\Models\Tag;

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
     * Find current page
     * @param int $page
     * @param int $count
     * @return Post[]
     */
    public function findPage(int $page, int $count): array;

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

    /**
     * Count post in DB
     * @return int
     */
    public function count():int;

    /**
     * Check is slug available
     * @param string $slug
     * @return bool
     */
    public function checkSlugAvailability(string $slug):bool ;

    /** Get category array ['id' => 'name']
     * @return array
     */
    public function getCategoriesKeysPairs(): array ;

}