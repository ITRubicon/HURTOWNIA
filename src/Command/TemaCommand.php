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
    name: 'tema:all',
    description: 'Ciągnie komplet danych z Temy',
)]
class TemaCommand extends Command
{
    private const COMMANDS = ['tema:stock', 'tema:customer', 'tema:user', 'tema:rodo:type', 'tema:car', 'tema:car:reserve', 'tema:car:stock', 'tema:fv', 'tema:fv:corrections', 'tema:fvz', 'tema:fvz:correction', 'tema:mm', 'tema:order', 'tema:prepayment', 'tema:prepayment:corrections', 'tema:pz', 'tema:wz', 'tema:reserve', 'tema:service', 'tema:warehouse', 'tema:contact:rodo'];

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)')
            ->addArgument('dateFrom', InputArgument::OPTIONAL, 'Data początkowa (domyślnie pierwszy dzień miesiąca', date('Y-m-01'))
            ->addArgument('dateTo', InputArgument::OPTIONAL, 'Data końcowa (domyślnie dzień dzisiejszy)', date('Y-m-d'));
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $source = $input->getArgument('api');

        $start = new DateTime('now');

        $io->info(sprintf('Pobieram dane dla %s api', $source));

        foreach (self::COMMANDS as $cmd) {
            $command = $this->getApplication()->find($cmd);
            $inputArgs = $this->prepareArguments($cmd, $input);
            $io->info(sprintf('Trwa pobieranie: %s', $cmd));
            $command->run(new ArrayInput($inputArgs), $output);
            unset($command);
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
            case 'tema:order':
                $arguments['dateFrom'] = $input->getArgument('dateFrom');
                $arguments['dateTo'] = $input->getArgument('dateTo');
                break;
            default:
                break;
        }

        return $arguments;
    }
}
