<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(
        WeatherUtil $util,
        #[MapQueryParameter('country')] string $country,
        #[MapQueryParameter('city')] string $city,
        #[MapQueryParameter('format')] string $format,
        #[MapQueryParameter('twig')] bool $twig = false,
    ): Response {
        $measurements = $util->getWeatherForCountryAndCity($country, $city);
        if ($format === 'json') {
            if ($twig) {
                return $this->render('weather_api/index.json.twig', [
                    'city' => $city,
                    'country' => $country,
                    'measurements' => $measurements,
                ]);
            } else {
                return $this->json([
                    'country' => $country,
                    'city' => $city,
                    'measurements' => array_map(fn (Measurement $m) => [
                        'date' => $m->getDate()->format('Y-m-d'),
                        'celcius' => $m->getFahrenheit(),
                    ], $measurements)
                ]);
            }
        }
        if ($twig) {
            return $this->render('weather_api/index.csv.twig', [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ]);
        } else {
            $csv = "name, country, date, celsius\n";
            $csv .= implode(
                "\n",
                array_map(fn (Measurement $m) => sprintf(
                    '%s,%s,%s,%s',
                    $country,
                    $city,
                    $m->getDate()->format('Y-m-d'),
                    $m->getFahrenheit(),
                ), $measurements)
            );
            return new Response($csv, 200, []);
        }
    }
}
