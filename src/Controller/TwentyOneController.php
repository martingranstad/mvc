<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TwentyOneController extends AbstractController
{
    #[Route("/game", name: "game")]
    public function game(): Response
    {
        return $this->render('game.html.twig');
    }

    #[Route("/game/doc", name: "game-doc")]
    public function gameDoc(): Response
    {
        return $this->render('game-doc.html.twig');
    }
    #[Route("/twenty-one", name: "twenty-one")]
    public function twentyOne(): Response
    {
        return $this->render('game.html.twig');
    }
}