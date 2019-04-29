<?php


namespace App\Models\Repositories;


use App\Models\Tag;
use PDO;

class TagRepository extends BaseRepository implements TagRepositoryInterface
{

    /**
     * Find one Tag by id
     * @param int $id
     * @return Tag|null
     */
    public function findOneById(int $id): ?Tag
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tags WHERE id=:id LIMIT 1');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Tag::class, [$this->container]);
        $stmt->execute(['id' => $id]);
        $tag = $stmt->fetch();
        return $tag ? $tag : null;
    }

    /**
     * Find all Tags
     * @return array
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tags');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Tag::class, [$this->container]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Insert new Tag in DB
     * @param Tag $tag
     * @return Tag
     */
    public function create(Tag $tag): Tag
    {
        $stmt = $this->pdo->prepare('INSERT INTO `tags` (`title`, `slug`) VALUES (:title, :slug)');
        $stmt->execute([
            'title' => $tag->getTitle(),
            'slug' => $tag->getSlug()
        ]);
        $id = (integer)$this->pdo->lastInsertId();
        $tag = $this->findOneById($id);
        return $tag;
    }

    /**
     * Update Tag in DB
     * @param Tag $tag
     * @return bool
     */
    public function update(Tag $tag): bool
    {
        $stmt = $this->pdo->prepare('UPDATE `tags` SET `title` = :title, `slug` = :slug WHERE `id` = :id');
        $stmt->execute([
            'title' => $tag->getTitle(),
            'slug' => $tag->getSlug(),
            'id' => $tag->getId()
        ]);
        return (bool)$stmt->rowCount();

    }

    /**
     * Get all Tags count
     * @return int
     */
    public function count(): int
    {
        return $this->pdo->query('SELECT count(*) FROM tags')->fetchColumn();
    }

    /**
     * Check is slug available
     * @param string $slug
     * @return bool
     */
    public function checkSlugAvailability(string $slug): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tags WHERE slug=:slug');
        $stmt->execute(['slug' => $slug]);
        return !(bool)$stmt->rowCount();
    }

    /**
     * Get array ['id' => 'title']
     * @return array
     */
    public function getIdsTitlesPairs(): array
    {
        // TODO: Implement getIdsTitlesPairs() method.
    }

    /**
     * Check is Tag title available
     * @param string $title
     * @return bool
     */
    public function checkTitleAvailability(string $title): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tags WHERE title=:title');
        $stmt->execute(['title' => $title]);
        return !(bool)$stmt->rowCount();
    }

    /**
     * Delete Tag by id
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM tags WHERE id=:id');
        $stmt->execute(array('id' => $id));
        return (bool)$stmt->rowCount();
    }

    /**
     * Find current page
     * @param int $page
     * @param int $count
     * @return Tag[]
     */
    public function findPage(int $page, int $count): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM tags ORDER BY id DESC LIMIT :limit OFFSET :offset');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Tag::class, [$this->container]);
        $stmt->execute([
            'limit' => $count,
            'offset' => (($page-1)*$count)
        ]);
        return $stmt->fetchAll();
    }

    /**
     * @param int[] $ids
     * @return Tag[]
     */
    public function findManyByIds($ids = array()): array
    {
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM tags WHERE id IN ($in)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Tag::class, [$this->container]);
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    /**
     * Get all tags dependent by post
     * @param int $id
     * @return Tag[]
     */
    public function findTagsByPostId(int $id): array
    {
        $stmt = $this->pdo->prepare('SELECT tag_id FROM posts_tags WHERE post_id=:id');
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $stmt->execute(['id' => $id]);
        $tagsIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if(empty($tagsIds)){
            return [];
        }
        return $this->findManyByIds($tagsIds);
    }
}