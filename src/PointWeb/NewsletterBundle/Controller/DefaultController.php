<?php

namespace PointWeb\NewsletterBundle\Controller;

use PointWeb\NewsletterBundle\Entity\NewsletterMail;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * this function called from the front page and register the new user into the newsletterMail Table
     */
    public function newsletterAction(Request $request)
    {
        $entity = new NewsletterMail();
        $pattern = "/^[a-z0-9._-]+@[a-z0-9._-]{2,}\\.[a-z]{2,4}$/";
        $error = null;

        if ($request->getMethod() == "POST") {
            $email = $request->get('email');
            if (preg_match($pattern, $email)) {
                $em = $this->getDoctrine()->getEntityManager();
                $check = $em->getRepository('PointWebNewsletterBundle:NewsletterMail')->findOneBy(array('email' => $email));
                if (!$check) {
                    $entity->setRegisterDate(new \DateTime("now"));
                    $entity->setEmail($email);
                    $entity->setNewsletterGroup(null);
                    $em->persist($entity);
                    $em->flush();
                    return $this->redirect($this->generateUrl('point_web_app_homepage'));
                } else {
                    $error = 1;
                }

            } else {
                $error = 2;
            }
        }

        $request->getSession()->set('error', $error);

        return $this->render('PointWebAppBundle:Default:index.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * this function called from the front page and delete the user from the newsletterMail Table
     */
    public function removeAction(Request $request)
    {
        $error = "";

        if ($request->getMethod() == "POST") {
            $email = $request->get('email');
            $em = $this->getDoctrine()->getEntityManager();
            $toDelete = $em->getRepository('PointWebNewsletterBundle:NewsletterMail')->findOneBy(array('email' => $email));
            if ($toDelete) {
                $em->remove($toDelete);
                $em->flush();
                return $this->redirect($this->generateUrl('point_web_app_homepage'));
            }
            $error = "<p style='color: darkred;'> cette addresse email n'existe pas dans notre liste d'abonn�s </p>";
        }

        return $this->render('PointWebNewsletterBundle:Default:delete.html.twig', array('error' => $error));
    }
}
