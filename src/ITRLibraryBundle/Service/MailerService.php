<?php

namespace ITRLibraryBundle\Service;

class MailerService
{
    /* @var \Swift_Mailer */
    private $mailer;

    /* @var \Twig_Environment*/
    private $twig;

    /**
     * MailerService constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendPostsToSubscribers(array $posts, array $subscribers)
    {
        $content = $this->twig->render('ITRLibraryBundle:emails:posts.html.twig', ['posts' => $posts]);

        $message = \Swift_Message::newInstance()
            ->setSubject('Weekly library update')
            ->setFrom('library@intracto.com')
            ->setBody($content, 'text/html')
            ->addPart($content, 'text/plain');

        foreach ($subscribers as $subscriber) {
            $message->setTo($subscriber->getEmail());
            $this->mailer->send($message);
        }
    }
}