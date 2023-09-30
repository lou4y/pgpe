<?php

namespace App\Controller;
use App\Entity\Matiere;
use App\Entity\Professeur;
use App\Repository\ProfesseurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmploisController extends AbstractController
{
    #[Route('/emplois', name: 'app_emplois')]
    public function index(ProfesseurRepository $professeurRepository): Response
    {
        $prof= new Professeur();
        return $this->render('emplois/index.html.twig', [
            'professeurs' => $professeurRepository->findAll(),
        ]);
    }
}
