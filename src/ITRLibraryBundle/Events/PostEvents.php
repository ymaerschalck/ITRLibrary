<?php

namespace ITRLibraryBundle\Events;

final class PostEvents
{
    /**
     * @Event("ITRLibraryBundle\Events\PostEvent")
     */
    const PRE_CREATE = 'post.pre_create';

    /**
     * @Event("ITRLibraryBundle\Events\PostEvent")
     */
    const POST_CREATE = 'post.post_create';
}