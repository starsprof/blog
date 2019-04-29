<?php


namespace App\Models\Repositories;


use App\Models\Tag;

/**
 * Interface TagRepositoryInterface
 * @package App\Models\Repositories
 */
interface TagRepositoryInterface
{
    /**
     * Find one Tag by id
     * @param int $id
     * @return Tag|null
     */
    public function findOneById(int $id): ?Tag;

    /**
     * Find all Tags
     * @return Tag[]
     */
    public function findAll():array;


    /**
     * @param int[] $ids
     * @return Tag[]
     */
    public function findManyByIds($ids = array()): array;

    /**
     * Find tags page
     * @param int $page
     * @param int $count
     * @return Tag[]
     */
    public function findPage(int $page, int $count): array;

    /**
     * Insert new Tag in DB
     * @param Tag $tag
     * @return Tag
     */
    public function create(Tag $tag): Tag;

    /**
     * Update Tag in DB
     * @param Tag $tag
     * @return bool
     */
    public function update(Tag $tag): bool;

    /**
     * Delete Tag by id
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool;

    /**
     * Get all Tags count
     * @return int
     */
    public function count():int;

    /**
     * Check is Tag slug available
     * @param string $slug
     * @return bool
     */
    public function checkSlugAvailability(string $slug):bool ;

    /**
     * Check is Tag title available
     * @param string $title
     * @return bool
     */
    public function checkTitleAvailability(string $title):bool ;

    /**
     * Get array ['id' => 'title']
     * @return array
     */
    public function getIdsTitlesPairs(): array ;

    /**
     * Get all tags dependent by post
     * @param int $id
     * @return Tag[]
     */
    public function findTagsByPostId(int $id): array;

}