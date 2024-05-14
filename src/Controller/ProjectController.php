<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route("/proj", name: "project")]
    public function project(): Response
    {
        return $this->render('project/proj.html.twig');
    }


    #[Route("/proj/about", name: "about-project")]
    public function aboutProject(): Response
    {
        return $this->render('project/about.html.twig');
    }
}
