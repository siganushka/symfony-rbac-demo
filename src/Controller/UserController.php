<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/users", name="app_user")
     */
    public function index()
    {
        $entities = $this->userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'entities' => $entities,
        ]);
    }

    /**
     * @Route("/users/new", name="app_user_new")
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $entity = new User();

        $form = $this->createForm('App\Form\UserType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($entity, $entity->getPlainPassword());
            $entity->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', sprintf('The user #%d has been created!', $entity->getId()));

            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/eidt/{id}", name="app_user_edit")
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder, $id): Response
    {
        $entity = $this->userRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm('App\Form\UserType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $plainPassword = $entity->getPlainPassword()) {
                $password = $passwordEncoder->encodePassword($entity, $plainPassword);
                $entity->setPassword($password);
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', sprintf('The user #%d has been updated!', $entity->getId()));

            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/users/delete/{id}", name="app_user_delete")
     */
    public function delete(Request $request, $id)
    {
        $entity = $this->userRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('delete', $request->query->get('token'))) {
            $this->addFlash('danger', 'The CSRF token is invalid.');

            return $this->redirectToRoute('app_user');
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
            $this->addFlash('danger', 'The user has been used, unable to delete.');

            return $this->redirectToRoute('app_user');
        }

        $this->addFlash('success', sprintf('The user #%d has been deleted!', $id));

        return $this->redirectToRoute('app_user');
    }
}
