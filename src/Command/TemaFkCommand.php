<?php

namespace App\Command;

use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:fk:all',
    description: 'Ciągnie komplet danych z FK Temy. Parametry: nazwa api (opcja), dateFrom [domyślnie bieżący rok]',
)]
class TemaFkCommand extends Command
{
    use LockableTrait;

    private const COMMANDS = ['tema:fk:cechy', 'tema:fk:dokinfo', 'tema:fk:kont', 'tema:fk:zapisy', 'tema:fk:kontrahenci'];

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL')
            ->addArgument('dateFrom', InputArgument::OPTIONAL, 'Data początkowa (domyślnie pierwszy dzień roku', date('Y-01-01'))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return Command::SUCCESS;
        }

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
            '========= KONIEC ==========',
            'start: ' . $start->format('Y-m-d H:i:s'),
            'koniec: ' . $end->format('Y-m-d H:i:s'),
            'czas: ' . $end->diff($start)->format('%H:%I:%S'),
        ]);

        $this->release();
        return Command::SUCCESS;
    }

    private function prepareArguments(string $cmd, & $input)
    {
        $arguments = ['command' => $cmd];

        if(!empty($input->getArgument('api')))
            $arguments['api'] = $input->getArgument('api');

        if(!empty($input->getArgument('dateFrom')) && $cmd != 'tema:fk:kontrahenci')
            $arguments['dateFrom'] = $input->getArgument('dateFrom');

        return $arguments;
    }
}
