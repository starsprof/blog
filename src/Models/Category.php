<?php


namespace App\Models;


use App\Models\Repositories\CategoryRepositoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Class Category
 * @package App\Models
 */
class Category
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
        $this->categoryRepository = $container->get(CategoryRepositoryInterface::class);
    }

}