<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Displays measurements for city in country',
)]
class WeatherCityCommand extends Command
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
            ->addArgument('countryCode', InputArgument::REQUIRED, 'Country code')
            ->addArgument('cityName', InputArgument::REQUIRED, 'City name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countryCode = $input->getArgument("countryCode");
        $cityName = $input->getArgument("cityName");

        $location = $this->locationRepository->findOneBy([
            'country' => $countryCode,
            'city' => $cityName
        ]);

        if (!$location) {
            $io->error('No weather data available for the specified location.');
            return Command::FAILURE;
        }

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
