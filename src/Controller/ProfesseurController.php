<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Groupes;
use App\Entity\Niveau;
use App\Entity\Professeur;
use App\Form\ExcelProfUploadFormType;
use App\Form\ProfesseurType;
use App\Repository\ProfesseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/professeur')]
class ProfesseurController extends AbstractController
{
    #[Route('/', name: 'app_professeur_index', methods: ['POST','GET'])]
    public function index(ProfesseurRepository $professeurRepository,Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExcelProfUploadFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $excelFile = $form->get('excelFile')->getData();


            // Move the uploaded file to a temporary location
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $excelFile->move(sys_get_temp_dir(), basename($tempFile));

            // Read the Excel file
            $spreadsheet = IOFactory::load($tempFile);
            $worksheet = $spreadsheet->getActiveSheet();
            // Search for the location of the first cell containing "N° Inscription" in the column headers
            $searchValue = 'Nom';
            $searchedcolumn = null;
            $searchedrow = null;
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            $highestRowIndex = $worksheet->getHighestRow();
            for ($rowIndex = 1; $rowIndex <= $highestRowIndex; $rowIndex++) {
                for ($columnIndex = 1; $columnIndex <= $highestColumnIndex; $columnIndex++) {
                    $cellValue = $worksheet->getCell([$columnIndex, $rowIndex])->getValue();
                    if ($cellValue == $searchValue) {
                        $searchedcolumn = $columnIndex;
                        $searchedrow = $rowIndex;
                        break;
                    }
                }
            }
            if ($searchedcolumn !== null) {
                // Get the starting row and column of the table
                $startColumn = $searchedcolumn;
                $startRow = $searchedrow+1; // Assuming the table starts from the second row

                // Iterate through rows and save data to the database
                $highestRow = $worksheet->getHighestRow();
                for ($row = $startRow; $row <= $highestRow; $row++) {
                    $Professeurdata = [];
                    for ($columnIndex = $searchedcolumn; $columnIndex <= $highestColumnIndex; $columnIndex++) {
                        $cellValue = $worksheet->getCellByColumnAndRow($columnIndex, $row)->getValue();
                        $Professeurdata[] = $cellValue;
                    }

                    // Save $Professeurdata to the database
                    $Professeur = new Professeur();
                    $Professeur->setNom($Professeurdata[0]);
                    $Professeur->setPrenom($Professeurdata[1]);
                    $Professeur->setCin($Professeurdata[2]);
                    $Professeur->setMatricule($Professeurdata[3]);
                    $Professeur->setEmail($Professeurdata[4]);


                    // Save $etudiant to the database
                    $entityManager->persist($Professeur);
                }

                // Flush all persisted entities to the database
                $entityManager->flush();
            }

            // Delete the temporary file
            unlink($tempFile);

            return $this->redirectToRoute('app_professeur_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('professeur/index.html.twig', [
            'professeurs' => $professeurRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_professeur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $professeur = new Professeur();
        $form = $this->createForm(ProfesseurType::class, $professeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($professeur);
            $entityManager->flush();

            return $this->redirectToRoute('app_professeur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('professeur/new.html.twig', [
            'professeur' => $professeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_professeur_show', methods: ['GET'])]
    public function show(Professeur $professeur): Response
    {
        return $this->render('professeur/show.html.twig', [
            'professeur' => $professeur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_professeur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Professeur $professeur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfesseurType::class, $professeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_professeur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('professeur/edit.html.twig', [
            'professeur' => $professeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_professeur_delete', methods: ['POST'])]
    public function delete(Request $request, Professeur $professeur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$professeur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($professeur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_professeur_index', [], Response::HTTP_SEE_OTHER);
    }
}
