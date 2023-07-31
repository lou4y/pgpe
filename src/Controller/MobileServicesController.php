<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MobileServicesController extends AbstractController
{
    #[Route('/mobile/services', name: 'app_mobile_services')]
    public function index(): Response
    {
        return $this->render('mobile_services/index.html.twig', [
            'controller_name' => 'MobileServicesController',
        ]);
    }
}
