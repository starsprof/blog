<?php


namespace App\Models;


use Psr\Container\ContainerInterface;

class BaseModel
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

}