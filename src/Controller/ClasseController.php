<?php

namespace App\Controller;

use App\Entity\Groupes;
use App\Form\GroupesType;
use App\Repository\GroupesRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/groupes')]
class ClasseController extends AbstractController
{
    #[Route('/', name: 'app_classe_index', methods: ['GET', 'POST'])]
    public function index(NiveauRepository $niveauRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $Groupe = new Groupes();
        $form = $this->createForm(GroupesType::class, $Groupe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($Groupe);
            $entityManager->flush();

            return $this->redirectToRoute('app_classe_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('groupes/index.html.twig', [
            'Groupe' => $Groupe,
            'form' => $form,
            'Niveaux' => $niveauRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_classe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $classe = new Groupes();
        $form = $this->createForm(GroupesType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($classe);
            $entityManager->flush();

            return $this->redirectToRoute('app_classe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupes/new.html.twig', [
            'groupes' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_classe_show', methods: ['GET'])]
    public function show(Groupes $classe): Response
    {
        return $this->render('classe/show.html.twig', [
            'groupes' => $classe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_classe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Groupes $classe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GroupesType::class, $classe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_classe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('groupes/edit.html.twig', [
            'groupes' => $classe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_classe_delete', methods: ['POST'])]
    public function delete(Request $request, Groupes $classe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$classe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($classe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_classe_index', [], Response::HTTP_SEE_OTHER);
    }
}
