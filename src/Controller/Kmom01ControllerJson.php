<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Kmom01ControllerJson
{
    #[Route("/api", name: "api")]
    public function home(): Response
    {
        return $this->render('api.html.twig');
    }
    #[Route("/api/quote")]
    public function jsonQuote(): Response
    {
        $quotes = [
            "Det första citatet kommer innan det andra - Källa: En klok person",
            "Det andra citatet kommer efter det första - Källa: En klok person",
            "Det tredje citatet kommer efter det första - Källa: En mindre klok person",
        ];
        $quote = $quotes[array_rand($quotes)];

        $todaysDate = date("Y-m-d");
        $timeStamp = date("H:i:s");

        $data = [
            'quote' => $quote,
            'date' => $todaysDate,
            'time' => $timeStamp,
        ];
        
        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
