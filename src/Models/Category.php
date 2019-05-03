<?php


namespace App\Models;


use App\Models\Repositories\CategoryRepositoryInterface;
use Psr\Container\ContainerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use \Respect\Validation\Validator as v;

/**
 * Class Category
 * @package App\Models
 */
class Category extends BaseModel
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string|null
     */
    protected $image;
    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->categoryRepository = $this->container->get(CategoryRepositoryInterface::class);
    }

    /**
     * Validate Category name
     * @param string $name
     * @return array
     */
    public static function validateName(string $name): array
    {
        $errors = [];
        try {
            v::length(5)
                ->setName('Name')->assert($name);
        }catch (NestedValidationException $exception) {
            $errors[] = $exception->getFullMessage();
        }
        return $errors;
    }

    /**
     * Validate Category description
     * @param string $description
     * @return array
     */
    public static function validateDescription(string $description): array
    {
        $errors = [];
        try {
            v::length(50)
                ->setName('Description')->assert($description);
        }catch (NestedValidationException $exception) {
            $errors[] = $exception->getFullMessage();
        }
        return $errors;
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->name,
            $this->image,
            $this->description
        ]);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
            $this->image,
            $this->description
            )
            = unserialize($serialized);
    }
}