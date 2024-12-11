<?php

namespace App\Entity;

class Post
{

    public $id;
    public $title;
    public $content;
    public $createdAt;

    /**
     * @param $id
     * @param $title
     * @param $content
     * @param $createdAt
     */
    public function __construct($id, $title, $content, $createdAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }


}