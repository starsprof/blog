<?php


namespace App\Models;


class Comment
{
    protected $id;
    protected $body;
    protected $created_at;
    protected $approved_at;

    protected $user;
}