<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Entity\User;
use App\Repository\AbsenceRepository;
use App\Repository\EtudiantRepository;
use App\Repository\GroupesRepository;
use App\Repository\MatiereRepository;
use App\Repository\ProfesseurRepository;
use App\Repository\UserRepository;
use App\Services\DateTransformer;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/mobile/apis/groups/{professorId}', name: 'app_api_groups', methods: ['GET'])]
    public function getTodaysMatieres(ProfesseurRepository $professeurRepository,MatiereRepository $matiereRepository ,int $professorId, DateTransformer $dateTransformer): JsonResponse
    {   $professor = $professeurRepository->find($professorId);
        $matiere = $matiereRepository->findBy(['Professeur' => $professor],['debut' => 'ASC']);

        $data = [];
        $today= new \DateTime();
        // Set the time zone to Tunisia
        $tunisiaTimeZone = new \DateTimeZone('Africa/Tunis');
        $today->setTimezone($tunisiaTimeZone);

        $dateString = $today->format('d:m:Y');
        foreach ($matiere as $mat) {
            $groupe = [];
            $etudiants = [];
            if ($mat->getJour() == $dateTransformer->transformToDate($dateString))
            {
                $grp=$mat->getGroupe();
                $groupe[] = [
                    'id' => $grp->getId(),
                    'Nom' => $grp->getNom(),
                    'etudiants' => $etudiants,
];
                $data[] = [
                    'id' => $mat->getId(),
                    'Nom' => $mat->getNom(),
                    'jour' => $mat->getJour(),
                    'debut'=> $mat->getDebut()->format('H:i'),
                    'fin'=> $mat->getFin()->format('H:i'),
                    'groupe' => $groupe,
                ];
            }
        }
        return new JsonResponse($data);
    }
    #[Route('/mobile/apis/groups/all/{professorId}', name: 'app_api_all_groups', methods: ['GET'])]
    public function getAllMatieres(ProfesseurRepository $professeurRepository,MatiereRepository $matiereRepository ,int $professorId, AbsenceRepository $absenceRepository): JsonResponse
    {   $professor = $professeurRepository->find($professorId);
        $matiere = $matiereRepository->findBy(['Professeur' => $professor]);
        $data = [];
        foreach ($matiere as $mat) {
            $groupe = [];
            $etudiants = [];

            foreach ($mat->getGroupe()->getEtudiants() as $etudiant) {
                $nbabs=$absenceRepository->findBy(['etudiant' => $etudiant,'matiere'=>$mat,'present'=>false]);
                $etudiants[] = [
                    'id' => $etudiant->getId(),
                    'Nom' => $etudiant->getNomFr(),
                    'Prenom' => $etudiant->getPrenomFr(),
                    'status' => count($nbabs)>4 ? 'Elimine' : 'Non Elimine',
                    'nbAbsence' =>count($nbabs) ,
                ];
            }
                $grp=$mat->getGroupe();
                $groupe[] = [
                    'id' => $grp->getId(),
                    'Nom' => $grp->getNom(),
                    'etudiants' => $etudiants,
                ];
                $data[] = [
                    'id' => $mat->getId(),
                    'Nom' => $mat->getNom(),
                    'jour' => $mat->getJour(),
                    'debut'=> $mat->getDebut()->format('H:i'),
                    'fin'=> $mat->getFin()->format('H:i'),
                    'groupe' => $groupe,
                ];
            }

        return new JsonResponse($data);
    }

    #[Route('/mobile/apis/etudiants/abcence/{groupeId}&{matiereId}', name: 'app_api_Abs_etudiants', methods: ['GET'])]
    public function getEtudiantsAbsByGroup(int $groupeId,int $matiereId,MatiereRepository $matiereRepository, GroupesRepository $groupesRepository,AbsenceRepository $absenceRepository,EntityManagerInterface $entityManager): JsonResponse
    {   $groupe = $groupesRepository->find($groupeId);
        $etudiants=[];
        $today = new \DateTime();
        $dateOnly = new \DateTime($today->format('Y-m-d'));
        foreach ($groupe->getEtudiants() as $etudiant) {
            $ISPRESENT = new String_();
            $absence = $absenceRepository->findOneBy(['matiere' => $matiereId, 'Date' => $today, 'etudiant' => $etudiant->getId()]);
            if ($absence == null) {
                $ISPRESENT = "Absent";
                $absence = new Absence();
                $absence->setDate($dateOnly);
                $absence->setEtudiant($etudiant);
                $absence->setMatiere($matiereRepository->find($matiereId));
                $absence->setPresent(false);
                $entityManager->persist($absence);
                $entityManager->flush();

            } else {
                $ISPRESENT = $absence->isPresent()  ? "Present" : "Absent";
            }

         $etudiants[]=[
                'id' => $etudiant->getId(),
                'Nom' => $etudiant->getNomFr(),
                'Prenom' => $etudiant->getPrenomFr(),
                'status' => $ISPRESENT,
            ];
        }
        return new JsonResponse($etudiants);
    }

    #[Route('/mobile/apis/etudiants/abcence', name: 'app_api_set_etudiants_abs', methods: ['POST','GET'])]
    public function SetEtudiantAbs(Request $request,EtudiantRepository $etudiantRepository,MatiereRepository $matiereRepository ,AbsenceRepository $absenceRepository ,EntityManagerInterface $entityManager): JsonResponse
    {
        $absences=[];
        // Get the JSON payload from the request
        $jsonData = $request->getContent();
        // Decode the JSON data into a PHP associative array
        $data = json_decode($jsonData, true);

        if ($data === null) {
            // Handle invalid JSON
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }
        $etudiants=[];
        // Process the etudiants data and update their status
        // Access the data elements
        $groupId = $data['groupId'];
        $matiereId = $data['matiereId'];
        $etudiants = $data['etudiants'];

        // Process etudiants data

        foreach ($etudiants as $etudiant) {
            $id = $etudiant['id'];
            $statut = $etudiant['statut'];

            // Find if absence exists for this etudiant and matiere combination
            $existingAbsence = $absenceRepository->findOneBy(['etudiant' => $id, 'matiere' => $matiereId , 'Date' => new \DateTime()] );

            if ($existingAbsence) {
                // Modify the existing absence
                $existingAbsence->setPresent($statut);
            } else {
                // Create a new absence if it doesn't exist
                $absence = new Absence();
                $absence->setEtudiant($etudiantRepository->find($id));
                $absence->setMatiere($matiereRepository->find($matiereId));
                $absence->setDate(new \DateTime());
                $absence->setPresent($statut);

                $entityManager->persist($absence);
            }

            $entityManager->flush();
        }

        // Return a response indicating the successful update
        return new JsonResponse(['message' => 'Absence updated successfully'], 200);
    }


    #[Route('/mobile/apis/Professeur/Register', name: 'app_api_reg_prof', methods: ['POST','GET'])]
    public function RegisterProfesseur(Request $request,ProfesseurRepository $professeurRepository,EntityManagerInterface $entityManager,UserRepository $repository): JsonResponse
    {
        $jsonData = $request->getContent();
        // Decode the JSON data into a PHP associative array
        $data = json_decode($jsonData, true);

        if ($data === null) {
            // Handle invalid JSON
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }
        $Email = $data['Email'];
        $Password = $data['Password'];
        if (!$professeurRepository->findOneBy(['email' => $Email])) {
            return new JsonResponse(['error' => 'cet e-mail n\'est pas l\'e-mail d\'un professeur inscrit'], 400);
        }
        if ($repository->findOneBy(['email' => $Email])) {
            return new JsonResponse(['error' => 'Cet e-mail est déjà enregistré! utilise-en un autre.'], 400);
        }

        $user = new User();
        $user->setEmail($Email);
        $user->setPassword($Password);
        $user->setRoles(['PROF']);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'compte enregistré avec succès'], 200);
    }

    #[Route('/mobile/apis/Professeur/Login', name: 'app_api_Login_prof', methods: ['POST','GET'])]

    public function LoginProfesseur(Request $request, ProfesseurRepository $professeurRepository, EntityManagerInterface $entityManager, UserRepository $repository): JsonResponse
    {
        $jsonData = $request->getContent();
        // Decode the JSON data into a PHP associative array
        $data = json_decode($jsonData, true);

        if ($data === null) {
            // Handle invalid JSON
            return new JsonResponse(['error' => 'Invalid JSON data'], 400);
        }
        $Email = $data['Email'];
        $Password = $data['Password'];

        $user = $repository->findOneBy(['email' => $Email]);

        if ($user !== null && password_verify($Password, $user->getPassword())) {
            // Return professor data without a token
            $prof = $professeurRepository->findOneBy(['email' => $Email]);
            $professorData = [
                'id' => $prof->getId(),
                'nom' => $prof->getNom(),
                'prenom' => $prof->getPrenom(),
                'email'  => $prof->getEmail()  ,
            ];

            return new JsonResponse(['professor' => $professorData], 200);
        } else {
            return new JsonResponse(['error' => 'Verify email and password'], 401);
        }
    }

}
