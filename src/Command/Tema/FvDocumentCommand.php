<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\FvDocumentRepository;
use App\Repository\Tema\FvItemRepository;
use App\Repository\Tema\FvSetProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:fv',
    description: 'Pobiera faktury sprzedaży i poycje z nich. Parametry: nazwa api (opcja)',
)]
class FvDocumentCommand extends BaseApiCommand
{
    private $docRepo;
    private $itemRepo;
    private $setProduct;
    protected $producerName = 'Tema';

    public function __construct(FvDocumentRepository $docRepo, FvItemRepository $itemRepo, FvSetProductRepository $setProduct, SourceAuthRepository $apiAuthRepo, ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->docRepo = $docRepo;
        $this->itemRepo = $itemRepo;
        $this->setProduct = $setProduct;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('api', InputArgument::OPTIONAL, 'Nazwa api (ALL lub brak nazwy dla wszystkich)', 'ALL');
        ;
    }

    protected function fetch(IConnection $api, SymfonyStyle &$io)
    {
        $this->docRepo->setSource($api);
        $this->itemRepo->setSource($api);
        $this->setProduct->setSource($api);
        $fetchedRows = $this->docRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $itemsCount = count($fetchedRows['items']);
        if ($itemsCount > 0) {
            $io->info(sprintf('Pobrano %s pozycji z dokumentów', $itemsCount));
            $this->itemRepo->saveItems($fetchedRows['items']);
        }
        
        $setProductsCount = count($fetchedRows['setProducts']);
        if ($setProductsCount > 0) {
            $io->info(sprintf('Pobrano %s pozycji pakietowych', $setProductsCount));
            $this->setProduct->saveItems($fetchedRows['setProducts']);
        }

        unset($fetchedRows);
    }

    protected function clearTable()
    {
        $this->docRepo->clearTable();
        $this->itemRepo->clearTable();
    }
}