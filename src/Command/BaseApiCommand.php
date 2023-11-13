<?php

namespace App\Command;

use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use DateTime;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class BaseApiCommand extends Command
{
    use LockableTrait;
    protected SourceAuthRepository $apiAuthRepo;
    protected $producerName = null;
    protected $cmdArgs;
    protected ApiFetchErrorRepository $errorRepo;
    protected $dateFrom;
    protected $dateTo;

    public function __construct(SourceAuthRepository $authRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct();
        $this->apiAuthRepo = $authRepo;
        $this->errorRepo = $errorRepo;
    }

    protected abstract function fetch(IConnection $api, SymfonyStyle &$io);
    protected abstract function clearTable();

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return Command::SUCCESS;
        }

        $io = new SymfonyStyle($input, $output);
        $this->cmdArgs = $input->getArguments();
        $apiSources = $this->getApiAuth($this->cmdArgs['api']);
        $this->dateFrom = $this->cmdArgs['dateFrom'] ?? null;
        $this->dateTo = $this->cmdArgs['dateTo'] ?? null;
        $apiSourcesCount = count($apiSources);

        if (empty($this->producerName)) {
            $io->error('Ustaw w klasie wartość pola $producerName');
            throw new Exception('Ustaw w klasie wartość pola $producerName');
            return Command::FAILURE;
        }

        if (!$apiSources) {
            $io->error('Nie znalazłem żadnego api');
            throw new Exception('Nieznana nazwa parametru api');
            return Command::FAILURE;
        }

        $start = new DateTime('now');

        $msg = 'Znalezione api:';
        for ($i = 0; $i < $apiSourcesCount; $i++) {
            $msg .= ' ' . $apiSources[$i]->getName() . ',';
        }

        $io->info($msg);

        $io->info('Czyszczę stare błędy pobierania');
        $io->info('Usunąłem ' . $this->errorRepo->clearErrors() . ' wpisów');

        $io->info('Pobieram dane');

        for ($i = 0; $i < $apiSourcesCount; $i++) {
            if ($i === 0)
                $this->clearTable();

            $io->info(sprintf('Pobieram dla %s', $apiSources[$i]->getName()));

            $this->fetch($apiSources[$i], $io);
            unset($apiSources[$i]);
        }

        $io->success('Koniec pracy');
        $end = new DateTime('now');
        $io->info([
            'start: ' . $start->format('Y-m-d H:i:s'),
            'koniec: ' . $end->format('Y-m-d H:i:s'),
            'czas: ' . $end->diff($start)->format('%H:%I:%S'),
            'błędy requestów: ' . $this->errorRepo->getErrorsCount(),
        ]);

        $this->release();
        return Command::SUCCESS;
    }

    protected function getApiAuth($source = null)
    {
        $apiSources = null;

        if (!empty($source) && strtoupper($source) !== 'ALL')
            $apiSources = $this->apiAuthRepo->findBy(['name' => $source]);
        else
            $apiSources = $this->apiAuthRepo->findBy(['producer' => $this->producerName]);

        return $apiSources;
    }
}
