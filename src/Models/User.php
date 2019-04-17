<?php


namespace App\Models;


/**
 * Class User
 * @package App\Models
 */
class User
{
    /**
     * @var integer
     */
    protected $id;
    /**
     * @var string
     */
    protected $email;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var string|null
     */
    protected $name;
    /**
     * @var string|null
     */
    protected $avatar;
}