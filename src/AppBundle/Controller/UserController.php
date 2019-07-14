<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;

class UserController extends FOSRestController {
    /**
     * @Rest\Post("/user")
     */
    public function postAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $data = new User;

        $name = $request->get('name');
        $username = $request->get('username');
        $plainPassword = $request->get('password');
        $roles = $request->get('roles');
        
        if (empty($name) || empty($username) || empty($plainPassword) || empty($roles))
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE); 
        }

        $encoder = $this->get('security.encoder_factory')->getEncoder(User::class);
        $encodedPassword = $encoder->encodePassword($plainPassword, null);

        $data->setName($name);
        $data->setUsername($username);
        $data->setPassword($encodedPassword);
        $data->setRoles($roles);

        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        
        return new View("User added successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/user")
     */
    public function getAction()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');

        $restresult = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        
        if ($restresult === null)
        {
            return new View("There are no users", Response::HTTP_NOT_FOUND);
        }
        
        return $restresult;
    }

    /**
     * @Rest\Get("/user/{id}")
     */
    public function idAction($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');
        
        $singleresult = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        
        if ($singleresult === null) 
        {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        }
        
        return $singleresult;
    }

    /**
     * @Rest\Delete("/user/{id}")
     */
    public function deleteAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $data = new User;
        
        $sn = $this->getDoctrine()->getManager();   
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        
        if (empty($user))
        {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        }
        else 
        {
            $sn->remove($user);
            $sn->flush();
        }
        return new View("User deleted successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/user/{id}")
     */
    public function updateAction($id, Request $request)
    { 
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');

        $data = new User;
        
        $name = $request->get('name');
        $username = $request->get('username');
        $plainPassword = $request->get('password');
        $roles = $request->get('roles');

        $sn = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        
        if (empty($user))
        {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        } 
        elseif (!empty($name) || !empty($username) || !empty($plainPassword) || !empty($roles))
        {
            if (!empty($name))
            {
                $user->setName($name);
            }

            if (!empty($username))
            {
                $user->setUsername($username);
            }
            
            if (!empty($plainPassword))
            {
                $encoder = $this->get('security.encoder_factory')->getEncoder(User::class);
                $encodedPassword = $encoder->encodePassword($plainPassword, null);
                $user->setPassword($encodedPassword);
            }

            if (!empty($roles))
            {
                $user->setRoles($roles);
            }

            $sn->flush();
            
            return new View("User updated successfully", Response::HTTP_OK);
        }
        else
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE); 
        }
    }
}