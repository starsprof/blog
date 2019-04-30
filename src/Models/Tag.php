<?php


namespace App\Models;



use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

/**
 * Class Tag
 * @package App\Models
 */
class Tag extends BaseModel
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $slug;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public static function validate(Tag $tag):array
    {
        $errors = [];
        try{
            v::length(3)
                ->setName('Title')
                ->assert(trim($tag->getTitle()));
        }catch (NestedValidationException $exception) {
            $errors['title'] = $exception->getFullMessage();
        }finally {
            try {
                v::slug()
                    ->setName('Slug')
                    ->assert($tag->getSlug());
            } catch (NestedValidationException $exception) {
                $errors['slug'] = $exception->getFullMessage();
            }
        }
        foreach ($errors as &$error)
        {
            $error = ltrim( $error, '- ');
        }
        return $errors;
    }

    /**
     * Get Random tags
     * @param array $tags
     * @param int $count
     * @return array
     */
    public static function getRandomTags(array $tags, int $count): array
    {
        $allKeys = array_keys($tags);
        $highKey = count($allKeys) - 1;
        $keys = $elements = array();
        $numElements = 0;
        while ($numElements < $count) {
            $num = mt_rand(0, $highKey);
                if (isset($keys[$num])) {
                    continue;
                }
                $keys[$num] = true;
            $elements[] = $tags[$allKeys[$num]];
            $numElements++;
        }
        return $elements;
    }
}