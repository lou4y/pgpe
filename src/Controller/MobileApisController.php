<?php

namespace App\Controller;

use App\Entity\Matiere;
use App\Repository\GroupesRepository;
use App\Repository\MatiereRepository;
use App\Repository\ProfesseurRepository;
use ContainerAZZd5M1\getGroupesRepositoryService;
use ContainerFmmIfJO\getEtudiant2Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MobileApisController extends AbstractController
{
    #[Route('/mobile/apis', name: 'app_mobile_apis')]
    public function index(): Response
    {
        return $this->render('mobile_apis/index.html.twig', [
            'controller_name' => 'MobileApisController',
        ]);
    }
    #[Route('/api/groups/{professorId}', name: 'app_api_groups', methods: ['GET'])]
    public function getGroupsByProfessor(ProfesseurRepository $professeurRepository,MatiereRepository $matiereRepository ,int $professorId, GroupesRepository $groupesRepository): JsonResponse
    {   $professor = $professeurRepository->find($professorId);
        $matiere = $matiereRepository->findBy(['Professeur' => $professor]);

        $data = [];
        $groupe = [];
        $etudiants = [];
        foreach ($matiere as $mat) {
            $grp=$mat->getGroupe();
            foreach ($grp->getEtudiants() as $etudiant) {
            $etudiants[]=[
              'id' => $etudiant->getId(),
                'Nom' => $etudiant->getNomFr(),
                'Prenom' => $etudiant->getPrenomFr(),
                'statut' => 'true',
            ];
            }

            $groupe[] = [
                'id' => $grp->getId(),
                'Nom' => $grp->getNom(),
                'etudiants' => $etudiants,];

            $data[] = [
                'id' => $mat->getId(),
                'Nom' => $mat->getNom(),
                'groupe' => $groupe,
            ];
        }
        return new JsonResponse($data);
    }
}
