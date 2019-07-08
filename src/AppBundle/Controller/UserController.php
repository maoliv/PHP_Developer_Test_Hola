<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;

class UserController extends Controller {

    /**
     * @Route("/create-user")
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        /*$user = new User();

        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, ['required' => true])
            ->add('username', TextType::class, ['required' => true])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat password'],
                'required' => true,
                'invalid_message' => 'The password fields must match.',
            ])
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'ADMIN' => 'ADMIN',
                    'PAGE_1' => 'PAGE_1',
                    'PAGE_2' => 'PAGE_2',
                ],
                'required' => true,
            ])
            ->add('save', SubmitType::class, array('label' => 'New User'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $user = $form->getData();

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirect('/view-user/' . $user->getId());
        }

        return $this->render(
            'user/edit.html.twig',
            array('form' => $form->createView())
        );*/
    }

    /**
     * @Route("/user/{id}")
     */   
    public function viewAction($id)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);

        $response = new JsonResponse();

        if ($user)
        {
            $data = array(
                'status' => 'ok',
                'user' => array(
                    'name' => $user->getName(),
                    'username' => $user->getUsername(),
                    'roles' => $user->getRoles()
                )
            );
        } 
        else 
        {
            $data = array(
                'status' => 'error', 
                'message' => 'There are no users with the following id: ' . $id
            );
        }

        $response->setData($data);

        return $response;
    }

    /**
     * @Route("/users")
     */  
    public function showAction()
    {
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        $response = new JsonResponse();

        $data = array(
            'status' => 'ok',
            'count' => count($users),
            'users' => array()
        );

        if ($users)
        {
            foreach ($users as $user)
            {
                array_push(
                    $data['users'], 
                    array(
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                        'username' => $user->getUsername(),
                        'roles' => $user->getRoles()
                    )
                );
            }
        }

        $response->setData($data);

        return $response;
    }

    /**
     * @Route("/delete-user/{id}")
     */ 
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);

        $response = new JsonResponse();

        if ($user)
        {
            $em->remove($user);
            $em->flush();

            $data = array(
                'status' => 'ok',
                'user' => array(
                    'id' => $id
                )
            );
        } 
        else 
        {
            $data = array(
                'status' => 'error', 
                'message' => 'There are no users with the following id: ' . $id
            );
        }

        $response->setData($data);

        return $response;
    }

    /**
    * @Route("/update-article/{id}")
    */  
    public function updateAction(Request $request, $id)
    {

      $em = $this->getDoctrine()->getManager();
      $article = $em->getRepository('AppBundle:Article')->find($id);

      if (!$article) {
        throw $this->createNotFoundException(
        'There are no articles with the following id: ' . $id
        );
      }

      $form = $this->createFormBuilder($article)
        ->add('title', TextType::class)
        ->add('author', TextType::class)
        ->add('body', TextareaType::class)
        ->add('url', TextType::class,
        array('required' => false, 'attr' => array('placeholder' => 'www.example.com')))
        ->add('save', SubmitType::class, array('label' => 'Update'))
        ->getForm();

      $form->handleRequest($request);

      if ($form->isSubmitted()) {

        $article = $form->getData();
        $em->flush();

        return $this->redirect('/view-article/' . $id);

      }

      return $this->render(
        'article/edit.html.twig',
        array('form' => $form->createView())
        );

    }
}