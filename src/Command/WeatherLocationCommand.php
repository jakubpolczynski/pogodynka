<?php

namespace App\Command;

use App\Repository\LocationRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\WeatherUtil;

#[AsCommand(
    name: 'weather:location',
    description: 'Get the weather forecast for a specific location'
)]
class WeatherLocationCommand extends Command
{
    private $weatherUtil;
    private $locationRepository;

    public function __construct(WeatherUtil $weatherUtil, LocationRepository $locationRepository)
    {
        $this->weatherUtil = $weatherUtil;
        $this->locationRepository = $locationRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('locationId', InputArgument::REQUIRED, 'The ID of the location');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locationId = $input->getArgument("locationId");
        $location = $this->locationRepository->find(($locationId));

        $forecasts = $this->weatherUtil->getWeatherForLocation($location);

        if (!$forecasts) {
            $io->error('No weather data available for the specified location.');
            return Command::FAILURE;
        }

        $io->title("Weather Forecast for Location:" . $location->getCity());
        foreach ($forecasts as $forecast) {
            $formattedString = sprintf(
                "%s, %s°C",
                $forecast->getDate()->format('Y-m-d'),
                $forecast->getCelsius()
            );
            $io->writeln($formattedString);
        }

        $io->success('Weather forecast retrieved successfully.');

        return Command::SUCCESS;
    }
}
