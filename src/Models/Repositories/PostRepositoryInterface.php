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

    /**
     * Assign tags to array (many to many)
     * @param int $postId
     * @param array $tagsIds
     */
    public function assignTagsToPost(int $postId, array $tagsIds):void ;

    /**
     * Find last published posts
     * @param int $count
     * @param int $offset
     * @return Post[]
     */
    public function findLastPublished(int $count, int $offset = 0): array ;

    /**
     * @param int $count
     * @return array
     */
    public function getRandomPublished(int $count): array;

    /**
     * Find one Post by slug
     * @param string $slug
     * @return Post|null
     */
    public function findOneBySlug(string $slug): ?Post;

    /**
     * Find all published post by category Id
     * @param int $categoryId
     * @param int $count
     * @param int $offset
     * @return Post[]
     */
    public function findPublishedByCategoryId(int $categoryId, int $count, int $offset = 0): array ;

    /**
     * Find all published Post by tag Id
     * @param int $tagId
     * @param int $count
     * @param int $offset
     * @return array
     */
    public function findPublishedByTagId(int $tagId, int $count, int $offset = 0): array;

    /**
     * Find posts by array ids
     * @param array $ids
     * @param bool $isPublishedOnly
     * @return array
     */
    public function findManyByIds(array $ids = array(), bool $isPublishedOnly = false): array;
}