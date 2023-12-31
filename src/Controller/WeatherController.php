<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Location;
use App\Service\WeatherUtil;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}/{country}', name: 'app_weather', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_WEATHER_CITY')]
    public function city(
        Location $location,
        WeatherUtil $util
    ): Response {
        $measurements = $util->getWeatherForLocation($location);

        return $this->render('weather/city.html.twig', [
            'location' => $location,
            'measurements' => $measurements,
        ]);
    }
}
