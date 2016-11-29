<?php

namespace ITRLibraryBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeeklyMailCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('itrlibrary:weekly_mail')
            ->setDescription('Send mails to all subscribers');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $sevenDaysAgo = new \DateTime('-7days');

        $posts = $em->getRepository('ITRLibraryBundle:Post')->getPostsSince($sevenDaysAgo);

        if (!empty($posts)) {
            $subscribers = $em->getRepository('ITRLibraryBundle:Subscriber')->findAll();

            $this->getContainer()->get('itrlibrary.service.mailer')->sendPostsToSubscribers($posts, $subscribers);
        }

    }
}
