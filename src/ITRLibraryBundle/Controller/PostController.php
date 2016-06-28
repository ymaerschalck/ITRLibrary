<?php

namespace ITRLibraryBundle\Controller;

use ITRLibraryBundle\Entity\Subscriber;
use ITRLibraryBundle\Events\PostEvent;
use ITRLibraryBundle\Events\PostEvents;
use ITRLibraryBundle\Form\SubscriberType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ITRLibraryBundle\Entity\Post;
use ITRLibraryBundle\Form\PostType;

/**
 * Post controller.
 *
 * @Route("/")
 */
class PostController extends Controller
{
    /**
     * Lists all Post entities.
     *
     * @Route("/", name="post_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $subscriber = new Subscriber();
        $subscribeForm = $this->createForm(SubscriberType::class, $subscriber);

        $em = $this->get('doctrine.orm.entity_manager');
        $qb = $em->createQueryBuilder();
        $qb->select(['p', 't'])
            ->from('ITRLibraryBundle:Post', 'p')
            ->leftJoin('p.tags', 't');

        $value = $request->query->get('value', false);
        if ($value) {
            $qb->where($qb->expr()->like('p.title', ':value'));
            $qb->where($qb->expr()->like('p.url', ':value'));
            $qb->orWhere($qb->expr()->like('t.name', ':value'));
            $qb->setParameter('value', '%'.$value.'%');
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('ITRLibraryBundle:post:index.html.twig', array(
            'pagination' => $pagination,
            'subscribeForm' => $subscribeForm->createView(),
        ));
    }

    /**
     * Creates a new Post entity.
     *
     * @Route("/new", name="post_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $dispatcher = $this->get('event_dispatcher');
            $event = new PostEvent($post);

            $dispatcher->dispatch(PostEvents::PRE_CREATE, $event);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $dispatcher->dispatch(PostEvents::POST_CREATE, $event);

            return $this->redirectToRoute('post_index');
        }

        return $this->render('ITRLibraryBundle:post:new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", name="post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Post $post)
    {
        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('ITRLibraryBundle:post:edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     *
     * @Route("/{id}/delete", name="post_delete")
     */
    public function deleteAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        return $this->redirectToRoute('post_index');
    }

    /**
     * @Route("/{id}/upvote", name="post_upvote", defaults={"add": 1})
     * @Route("/{id}/downvote", name="post_downvote", defaults={"add": -1})
     * @Method({"GET"})
     */
    public function voteAction(Request $request, Post $post, $add)
    {
        $queryData = $request->query->all();
        $post->vote($add);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $response = $this->redirectToRoute('post_index', $queryData);
        $response->headers->setCookie(new Cookie($post->getHash(), $add));

        return $response;
    }
}
