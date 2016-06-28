<?php

namespace ITRLibraryBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ITRLibraryBundle\Entity\Subscriber;
use ITRLibraryBundle\Form\SubscriberType;

/**
 * Subscriber controller.
 *
 * @Route("/subscriber")
 */
class SubscriberController extends Controller
{
    /**
     * Creates a new Subscriber entity.
     *
     * @Route("/new", name="subscriber_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($subscriber);
            $em->flush();

            $this->addFlash('success', 'Succesvol ingeschreven');
            $response = $this->redirectToRoute('post_index');
            $response->headers->setCookie(new Cookie('subscribed', 1));

            return $response;
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }
        return $this->redirectToRoute('post_index');
    }
}
