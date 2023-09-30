<?php

namespace App\Controller;

use App\Repository\AbsenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(AbsenceRepository $absenceRepository): Response

    {
        $absenceData = [];
        $monthsdata = [];
        $monthsdata[] = [
            'septembre'=> count($absenceRepository->findByabsencesInMonth(8)),
            'octobre'=> count($absenceRepository->findByabsencesInMonth(10)),
            'novembre'=> count($absenceRepository->findByabsencesInMonth(11)),
            'decembre'=> count($absenceRepository->findByabsencesInMonth(12)),
            'janvier'=> count($absenceRepository->findByabsencesInMonth(1)),
            'fevrier'=> count($absenceRepository->findByabsencesInMonth(2)),
            'mars'=> count($absenceRepository->findByabsencesInMonth(3)),
            'avril'=> count($absenceRepository->findByabsencesInMonth(4)),
            'mai'=> count($absenceRepository->findByabsencesInMonth(5)),
        ];

        $monthsdata1[] = ['Janvier'=> 40,
            'fev'=> 70,
            'mar'=> 10,
            'avr'=> 22,
            'juin'=> 33,
        ];
        $absenceData[] = ['2023' => $monthsdata];
        $absenceData[] = ['2022' => $monthsdata1];

        $groupabsenceData = $absenceRepository->findByAbsencesOfGroupInMonth(8);
        $allgroupedata=$absenceRepository->findByAbsencesOfGroupInTotal();
        return $this->render('dashboard/index.html.twig', [
            'absenceData' => $absenceData,
            'groupabsenceData' => $groupabsenceData,
            'allgroupedata' => $allgroupedata,
        ]);
    }
}
