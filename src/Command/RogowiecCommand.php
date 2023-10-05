<?php

namespace App\Command;

use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rogowiec:all',
    description: 'Ciągnie komplet danych z Rogowca',
)]
class RogowiecCommand extends Command
{
    private const COMMANDS = ['rogowiec:branch', 'rogowiec:orgunit', 'rogowiec:saleunit', 'rogowiec:ageing:cars', 'rogowiec:ageing:parts', 'rogowiec:ageing:production', 'rogowiec:cars:orders', 'rogowiec:cars:sold', 'rogowiec:parts:sold', 'rrogowiec:service:sold', 'rogowiec:invoice', 'rogowiec:customer'];

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL')
            ->addArgument('dateFrom', InputArgument::OPTIONAL, 'Data początkowa (domyślnie pierwszy dzień miesiąca', date('Y-m-01'))
            ->addArgument('dateTo', InputArgument::OPTIONAL, 'Data końcowa (domyślnie dzień dzisiejszy)', date('Y-m-d'));
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $start = new DateTime('now');

        $io->info(sprintf('Pobieram dane dla %s api', $input->getArgument('api')));

        foreach (self::COMMANDS as $cmd) {
            $command = $this->getApplication()->find($cmd);
            $inputArgs = $this->prepareArguments($cmd, $input);
            $io->info(sprintf('Trwa pobieranie: %s', $cmd));
            $command->run(new ArrayInput($inputArgs), $output);
            unset($command, $inputArgs);
        }

        $end = new DateTime('now');

        $io->success([
            'start: ' . $start->format('Y-m-d H:i:s'),
            'koniec: ' . $end->format('Y-m-d H:i:s'),
            'czas: ' . $end->diff($start)->format('%H:%I:%S'),
        ]);

        return Command::SUCCESS;
    }

    private function prepareArguments(string $cmd, & $input)
    {
        $arguments = ['command' => $cmd];

        if(!empty($input->getArgument('api')))
            $arguments['api'] = $input->getArgument('api');

        switch ($cmd) {
            case 'rogowiec:branch':
            case 'rogowiec:orgunit':
            case 'rogowiec:ageing:cars':
            case 'rogowiec:customer':
                break;
            case 'rogowiec:ageing:parts':
            case 'rogowiec:ageing:production':
                $arguments['dateTo'] = $input->getArgument('dateTo');
                break;
            default:
                $arguments['dateFrom'] = $input->getArgument('dateFrom');
                $arguments['dateTo'] = $input->getArgument('dateTo');
                break;
        }

        return $arguments;
    }
}
