<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Form\AbsenceType;
use App\Repository\AbsenceRepository;
use App\Repository\GroupesRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/absence')]
class AbsenceController extends AbstractController
{
    #[Route('/', name: 'app_absence_index', methods: ['GET'])]
    public function index(AbsenceRepository $absenceRepository,GroupesRepository $groupesRepository): Response
    {
        return $this->render('absence/index.html.twig', [
            'absences' => $absenceRepository->findAll(),
            'groupes' => $groupesRepository->findAll(),
        ]);
    }

    #[Route('/download/{groupId}', name: 'download_absence', methods: ['GET'])]
    public function downloadAttendance(Request $request,int $groupId,GroupesRepository $groupesRepository,AbsenceRepository $absenceRepository): Response
    {
        $group = $groupesRepository->find($groupId);
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set the class name and school year/semester in the first two rows
        $sheet->setCellValue('A1', 'Année:');
        $sheet->setCellValue('B1', '2023');
        $sheet->setCellValue('C1', 'Semestre:');
        $sheet->setCellValue('D1', '1');
        $sheet->setCellValue('A2', 'Groupe:');
        $sheet->setCellValue('B2', $group->getNom());

        // Set up the header for the student attendance table
        $sheet->setCellValue('A4', 'Nom');
        $sheet->setCellValue('B4', 'Prenom');
        $column=3;
        foreach ($group->getMatieres() as $matiere) {
            $sheet->setCellValue([$column, 4], $matiere->getNom());
            $column++;
        }
        $studentData=[];
        // Fetch student and subject data from the database (replace with your actual query)
        foreach ($group->getEtudiants() as $etudiant) {
            $elimination=[];
            foreach ($group->getMatieres() as $matiere) {
                $absence = $absenceRepository->findBy(['etudiant'=>$etudiant,'matiere'=>$matiere,'present'=>false]);
                //lenght of absence
                $elimination[]=[
                    count($absence) >1?'elimine':'Non elimine',
                ];
            }
            $studentData[] = [
                $etudiant->getNomFr(),
                $etudiant->getPrenomfr(),
                $elimination
            ];
        }

        // Populate the table with student attendance data
        $row = 5; // Start from row 5 to skip the first 3 rows
        foreach ($studentData as $data) {
              $sheet->setCellValue('A' . $row, $data[0]); // Student name
              $sheet->setCellValue('B' . $row, $data[1]); // Subject
            $startColumn = 3;
            foreach ($data[2] as $absence) {
                $sheet->setCellValue([$startColumn, $row], $absence[0]); // Absence number
                $startColumn++;
            }
        // You can add more columns for absence numbers if needed
        $row++;
        }

        // Create a new Xlsx writer and save the file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Elimination_group_' . $group->getNom() . '.xlsx'; // Adjust the filename
        $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $filename;


        $writer->save($filePath);

        // Provide the file to the user for download
        $response = new Response(file_get_contents($filePath));
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');

        return $response;
    }




}
