<?php

namespace ITRLibraryBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupTagsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('itrlibrary:cleanup_tags')
            ->setDescription('Clear all unused tags');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $tags = $em->getRepository('ITRLibraryBundle:Tag')->findUnusedTags();

        $output->writeln(sprintf('%s tag(s) being removed.', count($tags)));

        foreach ($tags as $tag) {
            $em->remove($tag);
        }

        $em->flush();
    }
}
