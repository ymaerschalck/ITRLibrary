<?php

namespace ITRLibraryBundle\Events\Listener;

use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Transport\ApiClient;
use ITRLibraryBundle\Entity\Post;
use ITRLibraryBundle\Events\PostEvent;

class SlackListener
{
    /* @var ApiClient*/
    private $slackClient;

    private $settings;

    /**
     * SlackListener constructor.
     *
     * @param $slackClient
     * @param $settings
     */
    public function __construct(ApiClient $slackClient, $settings)
    {
        $this->slackClient = $slackClient;
        $this->settings = $settings;
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
        $payload->setText($message);
        $payload->setUsername($this->settings['bot_name']);
        $payload->setIconEmoji('books');

        $channels = $this->getPushableChannels($post);

        foreach ($channels as $channel) {
            $payload->setChannel($channel);
            $this->slackClient->send($payload);
        }
    }

    private function getPushableChannels(Post $post)
    {
        $pushableChannels = [$this->settings['default_channel']];

        foreach ($post->getTags() as $tag) {
            foreach ($this->settings['channel_tags'] as $channel_tag) {
                if (in_array($tag->getName(), $channel_tag['tags']) && !in_array($channel_tag['channel'], $pushableChannels)) {
                    $pushableChannels[] = $channel_tag['channel'];
                }
            }
        }

        return $pushableChannels;
    }
}
