<?php

namespace ITRLibraryBundle\Events\Listener;

use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Transport\ApiClient;
use ITRLibraryBundle\Events\PostEvent;

class SlackListener
{
    /* @var ApiClient*/
    private $slackClient;

    /**
     * SlackListener constructor.
     *
     * @param $slackClient
     */
    public function __construct(ApiClient $slackClient)
    {
        $this->slackClient = $slackClient;
    }

    public function sendSlackMessage(PostEvent $event)
    {
        $post = $event->getPost();
        $message = sprintf("New blogpost added: <%s|%s>. \n Created on: %s", $post->getUrl(), $post->getTitle(), $post->getCreatedAt()->format('d-m-Y'));

        if ($post->getWrittenAt()) {
            $message .= sprintf(' - Written on: %s', $post->getWrittenAt()->format('d-m-Y'));
        }

        if ($post->tagList()) {
            $message .= sprintf("\n Tags: %s", $post->tagList());
        }

        $payload = new ChatPostMessagePayload();
        $payload->setChannel('#library');
        $payload->setText($message);  // also supports Slack formatting
        $payload->setUsername('LibraryBot');
        $payload->setIconEmoji('books');

        $this->slackClient->send($payload);
    }
}
