<?php


namespace App\Models\Repositories;


use App\Models\Post;
use PDO;
use Psr\Container\ContainerInterface;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    /**
     * PostRepository constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }


    /**
     * Find one Post by Id
     * @param int $id
     * @return Post|null
     */
    public function findOneById(int $id): ?Post
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE id=:id LIMIT 1');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $stmt->execute(['id' => $id]);
        $post = $stmt->fetch();
        return $post ? $post : null;
    }

    /**
     * Find all Posts
     * @return Post[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete one Post by Id
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id=:id');
        $stmt->execute(array('id' => $id));
        return (bool)$stmt->rowCount();
    }

    /**
     * Save new Post
     * @param Post $post
     * @return Post
     */
    public function create(Post $post): Post
    {
        $stmt = $this->pdo->prepare('INSERT INTO posts (`title`, `slug`, `image`, `description`, `body`, `created_at`,
                   `updated_at`, `published_at`, `published`, `category_id`, `author_id`) VALUES (
                     :title, :slug, :image, :description, :body, :created_at,
                   :updated_at, :published_at, :published, :category_id, :author_id)');
        $stmt->execute([
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'image' => $post->getImage(),
            'description' => $post->getDescription(),
            'body' => $post->getBody(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'published_at' => $post->getPublishedAt()->format("Y-m-d H:i:s"),
            'published' => (int)$post->isPublished(),
            'category_id' => $post->getCategoryId(),
            'author_id' => $post->getAuthorId()
        ]);
        $tagsIds = $post->getTagsIds();
        $id = (integer)$this->pdo->lastInsertId();
        if (!empty($tagsIds)) {
            $this->assignTagsToPost($id, $tagsIds);
        }
        $post = $this->findOneById($id);
        return $post;
    }

    /**
     * Update Post
     * @param Post $post
     * @return bool
     */
    public function update(Post $post): bool
    {
        $stmt = $this->pdo->prepare('UPDATE posts SET
                title=:title, slug=:slug, image=:image, description=:description, body=:body, created_at=:created_at,
                updated_at=:updated_at, published_at=:published_at, published=:published, category_id=:category_id
                WHERE id=:id');
        $stmt->execute([
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'image' => $post->getImage(),
            'description' => $post->getDescription(),
            'body' => $post->getBody(),
            'created_at' => $post->getCreatedAt()->format("Y-m-d H:i:s"),
            'updated_at' => date('Y-m-d H:i:s'),
            'published_at' => $post->getPublishedAt()->format("Y-m-d H:i:s"),
            'published' => $post->isPublished(),
            'category_id' => $post->getCategoryId(),
            'id' => $post->getId()
        ]);
        $this->unassumingTagsForPost($post->getId());
        if(!empty($post->getTagsIds())){
            $this->assignTagsToPost($post->getId(), $post->getTagsIds());
        }
        return (bool)$stmt->rowCount();
    }

    /**
     * Find current page
     * @param int $page
     * @param int $count
     * @return Post[]
     */
    public function findPage(int $page, int $count): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts ORDER BY updated_at DESC LIMIT :limit OFFSET :offset');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $stmt->execute([
            'limit' => $count,
            'offset' => (($page-1)*$count)
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Count post in DB
     * @return int
     */
    public function count(): int
    {
        return $this->pdo->query('SELECT count(*) FROM posts')->fetchColumn();
    }

    /**
     * Check is slug available
     * @param string $slug
     * @return bool
     */
    public function checkSlugAvailability(string $slug): bool
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE slug=:slug');
        $stmt->execute(['slug' => $slug]);
        return !(bool)$stmt->rowCount();
    }

    /** Get category array ['id' => 'name']
     * @return array
     */
    public function getCategoriesKeysPairs(): array
    {
        $stmt = $this->pdo->query('SELECT `id`, `name` FROM categories');
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Assign tags to array (many to many)
     * @param int $postId
     * @param array $tagsIds
     */
    public function assignTagsToPost(int $postId, array $tagsIds): void
    {
        $this->pdo->beginTransaction();
        foreach ($tagsIds as $tagId)
        {
            $stmt = $this->pdo->prepare('INSERT INTO `posts_tags` (`post_id`, `tag_id`) 
                                       VALUES (:post_id, :tag_id)');
            $stmt->execute([
                'post_id' => $postId,
                'tag_id' => $tagId
            ]);
        }
        $this->pdo->commit();
    }

    private function unassumingTagsForPost($postId):void
    {
        $stmt = $this->pdo->prepare('DELETE FROM `posts_tags` WHERE `post_id` = :post_id');
        $stmt->execute(['post_id' => $postId]);

    }

    /**
     * Find last published posts
     * @param int $count
     * @param int $offset
     * @return Post[]
     */
    public function findLastPublished(int $count, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE `published` = 1 ORDER BY published_at DESC LIMIT :count OFFSET :offset');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $stmt->execute(['count' => $count, 'offset' => $offset]);
        return $stmt->fetchAll();
    }

    /**
     * @param int $count
     * @return array
     */
    public function getRandomPublished(int $count): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE `published` = 1 
                                ORDER BY RAND() LIMIT :count');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $stmt->execute(['count' => $count]);
        return $stmt->fetchAll();
    }


    /**
     * Find one Post by slug
     * @param string $slug
     * @return Post|null
     */
    public function findOneBySlug(string $slug): ?Post
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE slug=:slug LIMIT 1');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();
        return $post ? $post : null;
    }

    /**
     * Find all published post by category Id
     * @param int $categoryId
     * @param int $count
     * @param int $offset
     * @return Post[]
     */
    public function findPublishedByCategoryId(int $categoryId, int $count, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM posts WHERE 
                                              `published` = 1 AND `category_id` = :category_id
                                              ORDER BY published_at DESC LIMIT :count OFFSET :offset');
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $stmt->execute(['count' => $count, 'offset' => $offset, 'category_id' => $categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Find all published Post by tag Id
     * @param int $tagId
     * @param int $count
     * @param int $offset
     * @return array
     */
    public function findPublishedByTagId(int $tagId, int $count, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare('SELECT post_id FROM posts_tags WHERE tag_id=:tag_id 
                LIMIT :count OFFSET :offset');
        $stmt->setFetchMode(PDO::FETCH_NUM);
        $stmt->execute([
            'tag_id' => $tagId,
            'count' => $count,
            'offset' => $offset
        ]);
        $postIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $this->findManyByIds($postIds, true);
    }

    /**
     * Find posts by array ids
     * @param array $ids
     * @param bool $isPublishedOnly
     * @return array
     */
    public function findManyByIds(array $ids = array(), bool $isPublishedOnly = false): array
    {
        $in = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM posts WHERE id IN ($in) AND `published` = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Post::class, [$this->container]);
        $ids[] = (int) $isPublishedOnly;
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }
}