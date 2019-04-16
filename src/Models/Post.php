<?php


namespace App\Models;


class Post
{
    protected $id;
    protected $title;
    protected $slug;
    protected $images;
    protected $description;
    protected $body;
    protected $created_at;
    protected $updated_at;
    protected $published_at;
    protected $published;

    protected $category;
    protected $tags;
}