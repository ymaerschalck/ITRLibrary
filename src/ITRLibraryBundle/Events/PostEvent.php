<?php

namespace ITRLibraryBundle\Events;

use ITRLibraryBundle\Entity\Post;
use Symfony\Component\EventDispatcher\Event;

class PostEvent extends Event
{
    /* @var Post */
    private $post;

    /**
     * Constructs an event.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return mixed
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param mixed $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }
}
