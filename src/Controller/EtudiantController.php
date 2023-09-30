<?php

namespace App\Controller;
use App\Entity\Groupes;
use App\Entity\Niveau;
use App\Repository\GroupesRepository;
use App\Repository\NiveauRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\DBAL\Connection;
use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Form\ExcelProfUploadFormType;
use App\Repository\EtudiantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/etudiant')]
class EtudiantController extends AbstractController
{
    #[Route('/', name: 'app_etudiant_index', methods: ['GET','POST' ])]
    public function index(Request $request,EntityManagerInterface $entityManager,NiveauRepository $niveauRepository,GroupesRepository $groupesRepository): Response
    {
        $form = $this->createForm(ExcelProfUploadFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $excelFile = $form->get('excelFile')->getData();
            $separator = $form->get('separator')->getData();
            if ($separator == null) {
                $separator = " ";
            }

            // Move the uploaded file to a temporary location
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $excelFile->move(sys_get_temp_dir(), basename($tempFile));

            // Read the Excel file
            $spreadsheet = IOFactory::load($tempFile);
            $worksheet = $spreadsheet->getActiveSheet();
            // Search for the location of the first cell containing "N° Inscription" in the column headers
            $searchValue = 'N° Inscription';
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
                    $etudiantData = [];
                    for ($columnIndex = $searchedcolumn; $columnIndex <= $highestColumnIndex; $columnIndex++) {
                        $cellValue = $worksheet->getCellByColumnAndRow($columnIndex, $row)->getValue();
                        $etudiantData[] = $cellValue;
                    }
                    $niveau = new Niveau();
                    $groupe = new Groupes();
                    $dateDeNaissanceString = $etudiantData[8];
                    $dateDeNaissance = DateTimeImmutable::createFromFormat('d/m/Y', $dateDeNaissanceString);
                    $dateDeNaissanceFormatted = $dateDeNaissance->format('Y/m/d');

                   // Save $etudiantData to the database
                    $etudiant = new Etudiant();
                    $etudiant->setNInscriptionn((int) $etudiantData[0]);
                    $etudiant->setCin((int)$etudiantData[1]);
                    $etudiant->setNomAr($etudiantData[2]);
                    $etudiant->setPrenomAr($etudiantData[3]);
                    $etudiant->setNomFr($etudiantData[4]);
                    $etudiant->setPrenomFr($etudiantData[5]);
                    $etudiant->setSexe($etudiantData[6]);
                    $etudiant->setSituationFamiliale((int)$etudiantData[7]);
                    $etudiant->setDateDeNaissance($dateDeNaissance);
                    $etudiant->setLieuDeNaissanceAr($etudiantData[9]);
                    $etudiant->setLieuDeNaissanceFr($etudiantData[10]);
                    $etudiant->setStatut($etudiantData[11]);
                    $etudiant->setPasseport($etudiantData[12]);
                    $etudiant->setAdresseFr($etudiantData[13]);
                    $etudiant->setAdresseAr($etudiantData[14]);
                    $etudiant->setCodeGouvernorat($etudiantData[15]);
                    $etudiant->setEmail($etudiantData[16]);
                    $etudiant->setTelephoneFixe((int)$etudiantData[17]);
                    $etudiant->setTelephonePortable((int)$etudiantData[18]);
                    $etudiant->setCodeNatureBac((int)$etudiantData[19]);
                    $etudiant->setInscription($etudiantData[20]);
                    $parts = explode($separator, $etudiantData[21]);
                    $firstPart = trim($parts[0]);
                    if ($niveauRepository->findOneBy(['Nom'=>$firstPart]))
                    {
                        $niveau=$niveauRepository->findOneBy(['Nom'=>$firstPart]);
                    }else{
                        $niveau->setNom($firstPart);
                        $entityManager->persist($niveau);
                        $entityManager->flush();
                    }

                    if($groupesRepository->findOneBy(['Nom'=>$etudiantData[21]]))
                    {
                        $groupe=$groupesRepository->findOneBy(['Nom'=>$etudiantData[21]]);
                    }else{
                        $groupe->setNom($etudiantData[21]);
                        $groupe->setNiveau($niveau);
                        $entityManager->persist($groupe);
                        $entityManager->flush();
                    }
                    $etudiant->setGroupe($groupe);
                    // Save $etudiant to the database
                    $entityManager->persist($etudiant);
                }

                // Flush all persisted entities to the database
                $entityManager->flush();
            }

            // Delete the temporary file
            unlink($tempFile);

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('etudiant/index.html.twig', [
            'groupes' => $groupesRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_etudiant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etudiant);
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/new.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_show', methods: ['GET'])]
    public function show(Etudiant $etudiant): Response
    {
        return $this->render('etudiant/show.html.twig', [
            'etudiant' => $etudiant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etudiant/edit.html.twig', [
            'etudiant' => $etudiant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$etudiant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($etudiant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_etudiant_index', [], Response::HTTP_SEE_OTHER);
    }
}
