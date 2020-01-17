<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends AbstractController
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @Route("/roles", name="app_role")
     */
    public function index(): Response
    {
        $entities = $this->roleRepository->findAll();
        $translationDomain = $this->getParameter('rbac.translation_domain');

        return $this->render('role/index.html.twig', [
            'entities' => $entities,
            'translation_domain' => $translationDomain,
        ]);
    }

    /**
     * @Route("/roles/new", name="app_role_new")
     */
    public function new(Request $request): Response
    {
        $entity = new Role();

        $form = $this->createForm('App\Form\RoleType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', sprintf('The role #%d has been created!', $entity->getId()));

            return $this->redirectToRoute('app_role');
        }

        return $this->render('role/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/roles/edit/{id}", name="app_role_edit")
     */
    public function edit(Request $request, $id)
    {
        $entity = $this->roleRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm('App\Form\RoleType', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', sprintf('The role #%d has been Updated!', $id));

            return $this->redirectToRoute('app_role');
        }

        return $this->render('role/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/roles/delete/{id}", name="app_role_delete")
     */
    public function delete(Request $request, $id)
    {
        $entity = $this->roleRepository->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('delete', $request->query->get('token'))) {
            $this->addFlash('danger', 'The CSRF token is invalid.');

            return $this->redirectToRoute('app_role');
        }

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
            $this->addFlash('danger', 'The role has been used, unable to delete.');

            return $this->redirectToRoute('app_role');
        }

        $this->addFlash('success', sprintf('The role #%d has been deleted!', $id));

        return $this->redirectToRoute('app_role');
    }
}
