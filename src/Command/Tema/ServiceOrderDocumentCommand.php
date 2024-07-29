<?php

namespace App\Command\Tema;

use App\Command\BaseApiCommand;
use App\Entity\IConnection;
use App\Repository\ApiFetchErrorRepository;
use App\Repository\SourceAuthRepository;
use App\Repository\Tema\PackageItemFvRepository;
use App\Repository\Tema\PackageItemMechanicRepository;
use App\Repository\Tema\ServiceOrderCarRepository;
use App\Repository\Tema\ServiceOrderDocumentRepository;
use App\Repository\Tema\ServiceOrderEndDocumentRepository;
use App\Repository\Tema\ServiceOrderItemInvoiceRepository;
use App\Repository\Tema\ServiceOrderItemPackageItemRepository;
use App\Repository\Tema\ServiceOrderItemRepository;
use App\Repository\Tema\ServiceOrderMechanicRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'tema:service',
    description: 'Pobiera zlecenia serwisowe pozycje z nich. Parametry: nazwa api (opcja)',
)]
class ServiceOrderDocumentCommand extends BaseApiCommand
{
    private $docRepo;
    private $itemRepo;
    private $itemInvoiceRepo;
    private $endDocRepo;
    private $carRepo;
    private $mechanicRepo;
    private $packageRepo;
    private $packageItemFv;
    private $packageItemMechanic;
    protected $producerName = 'Tema';

    public function __construct(
        ServiceOrderDocumentRepository $docRepo,
        ServiceOrderItemRepository $itemRepo,
        ServiceOrderItemInvoiceRepository $itemInvoiceRepo,
        ServiceOrderEndDocumentRepository $endDocRepo,
        ServiceOrderCarRepository $carRepo,
        ServiceOrderMechanicRepository $mechanicRepo,
        ServiceOrderItemPackageItemRepository $packageRepo,
        PackageItemFvRepository $packageItemFv,
        PackageItemMechanicRepository $packageItemMechanic,
        SourceAuthRepository $apiAuthRepo,
        ApiFetchErrorRepository $errorRepo)
    {
        parent::__construct($apiAuthRepo, $errorRepo);
        $this->docRepo = $docRepo;
        $this->itemRepo = $itemRepo;
        $this->endDocRepo = $endDocRepo;
        $this->carRepo = $carRepo;
        $this->itemInvoiceRepo = $itemInvoiceRepo;
        $this->mechanicRepo = $mechanicRepo;
        $this->packageRepo = $packageRepo;
        $this->packageItemFv = $packageItemFv;
        $this->packageItemMechanic = $packageItemMechanic;
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
        $this->itemInvoiceRepo->setSource($api);
        $this->endDocRepo->setSource($api);
        $this->carRepo->setSource($api);
        $this->mechanicRepo->setSource($api);
        $this->packageRepo->setSource($api);
        $this->packageItemFv->setSource($api);
        $this->packageItemMechanic->setSource($api);

        $fetchedRows = $this->docRepo->fetch();
        $io->info(sprintf("Pobrano %s rekordów", $fetchedRows['fetched']));

        $itemsCount = count($fetchedRows['items']);
        if ($itemsCount > 0) {
            $io->info(sprintf('Pobrano %s pozycji z dokumentów', $itemsCount));

            $result = $this->itemRepo->saveItems($fetchedRows['items']);
            $io->info(sprintf('Zapisano %s pozycji z faktur', $result['fetched']));
/*             
            $res = $this->itemInvoiceRepo->saveInvoices($result['invoices']);
            $io->info(sprintf('Zapisano %s faktur przypisanych do pozycji zlecenia', $res));
            unset($result['invoices']);

            $res = $this->mechanicRepo->saveMechanics($result['workerHours']);
            $io->info(sprintf('Zapisano %s mechaników', $res));
            unset($result['workerHours']);
            
            $result = $this->packageRepo->saveItems($result['packageItems']);
            $io->info(sprintf('Zapisano %s pozycji z pakietów', $result['fetched']));

            $res = $this->packageItemFv->saveInvoices($result['invoices']);
            $io->info(sprintf('Zapisano %s faktur z pakietów', $res));
            
            $res = $this->packageItemMechanic->saveMechanics($result['workerHours']);
            $io->info(sprintf('Zapisano %s mechaników z pakietów', $res));
 */
            unset($result);
        }
        unset($fetchedRows['items']);

        $endDocsCount = count($fetchedRows['endDocs']);
        if ($endDocsCount > 0) {
            $io->info(sprintf('Pobrano %s pozycji z dokumentów końcowych', $endDocsCount));
            $this->endDocRepo->saveDocs($fetchedRows['endDocs']);
        }
        unset($fetchedRows['endDocs']);

        $carsCount = count($fetchedRows['cars']);
        if ($carsCount > 0) {
            $io->info(sprintf('Pobrano %s samochód', $carsCount));
            $this->carRepo->saveCars($fetchedRows['cars']);
        }
        unset($fetchedRows['cars']);
    }

    protected function clearTable()
    {
        $this->docRepo->clearTable();
        $this->itemRepo->clearTable();
        $this->endDocRepo->clearTable();
        $this->carRepo->clearTable();
    }
}