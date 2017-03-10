<?php

namespace ITRLibraryBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use ITRLibraryBundle\Entity\Post;
use ITRLibraryBundle\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private $em;

    /**
     * PostType constructor.
     *
     * @param $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('url')
            ->add('writtenAt', DateType::class, [
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ],
                'format' => 'ddMMyyyy',
                'required' => false,
                'years' => range(date("Y"), '1950'),
            ])
            ->add('tags', EntityType::class, array(
                'class' => Tag::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'class' => 'js-select2',
                ],
            ))
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'createNewTags']);
    }

    public function createNewTags(FormEvent $event)
    {
        $data = $event->getData();
        $tags = $data['tags'];
        $newTags = [];

        foreach ($tags as $tag) {
            if (is_numeric($tag)) {
                $newTags[] = $tag;
                continue;
            }

            $newTag = new Tag();
            $newTag->setName(strtolower($tag));
            $this->em->persist($newTag);
            $this->em->flush();

            $newTags[] = $newTag->getId();
        }

        $data['tags'] = $newTags;
        $event->setData($data);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Post::class,
        ));
    }
}
