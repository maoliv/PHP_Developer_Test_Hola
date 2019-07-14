<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', []);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/page/{number}", name="page", requirements={"number"="1|2"})
     */
    public function pageAction(Request $request, $number)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        /** 
         * if the user is not logged-in the page MUST redirect with a HTTP
         * response code 302 to the login page
         */
        if (false === $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new RedirectResponse('/login', 302);
        }
        
        /**
         * if the user is logged-in but do noy have the appropiate role an
         * error MUST be shown with a 403 HTTP response code
         */
        if (false === $securityContext->isGranted(['ROLE_ADMIN', 'ROLE_PAGE_' . $number])) {
            return $this->render('default/page.html.twig', 
                [
                    'error' => 'You must have ROLE_ADMIN or ROLE_PAGE_' .  $number . ' to access this page.',
                    'number' => $number
                ],
                new Response('Invalid role', 403)
            );
        }

        $user = $this->getUser();
        
        return $this->render('default/page.html.twig', [
            'error' => '',
            'user' => $user,
            'number' => $number,
        ]);
    }

    /**
     * @Route("/page/2", name="page2")
     */
    public function page2Action(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        /** 
         * if the user is not logged-in the page MUST redirect with a HTTP
         * response code 302 to the login page
         */
        if (false === $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new RedirectResponse('/login', 302);
        }
        
        /**
         * if the user is logged-in but do noy have the appropiate role an
         * error MUST be shown with a 403 HTTP response code
         */
        if (false === $securityContext->isGranted(['ROLE_ADMIN', 'ROLE_PAGE_2'])) {
            return $this->render('default/page2.html.twig', 
                ['error' => 'You must have ROLE_ADMIN or ROLE_PAGE_2 to access this page.'],
                new Response('Invalid role', 403)
            );
        }

        $user = $this->getUser();
        
        return $this->render('default/page2.html.twig', [
            'error' => '',
            'user' => $user,
        ]);
    }
}
