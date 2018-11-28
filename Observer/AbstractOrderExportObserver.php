<?php
namespace MageSuite\SampleOrderExport\Observer;

use Creativestyle\OrderExport\Services\Export\Converter\OrderCollection as OrderCollectionConverter;

abstract class AbstractOrderExportObserver
{
    /**
     * @var \Creativestyle\OrderExport\Repository\OrderRepository
     */
    protected $orderRepository;

    /**
     * @var \Creativestyle\OrderExport\Repository\ExportRepository
     */
    protected $exportRepository;

    /**
     * @var OrderCollectionConverter
     */
    protected $orderCollectionConverter;

    /**
     * @var \Creativestyle\OrderExport\Services\Export\Exporter\CSV
     */
    protected $exporter;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Creativestyle\OrderExport\Services\FTP\Uploader;
     */
    protected $uploader;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    private $eventManager;
    
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepositoryInterface;
    /**
     * @var \Creativestyle\OrderExport\Api\ExportStatusRepositoryInterface
     */
    protected $exportStatusRepository;

    public function __construct(
        \Creativestyle\OrderExport\Repository\OrderRepository $orderRepository,
        \Creativestyle\OrderExport\Repository\ExportRepository $exportRepository,
        \Creativestyle\OrderExport\Services\Export\Converter\OrderCollection $orderCollectionConverter,
        \Creativestyle\OrderExport\Services\Export\ExporterFactory $exporterFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Creativestyle\OrderExport\Services\FTP\Uploader $uploader,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        \Creativestyle\OrderExport\Api\ExportStatusRepositoryInterface $exportStatusRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->exportRepository = $exportRepository;
        $this->orderCollectionConverter = $orderCollectionConverter;
        $this->exporter = $exporterFactory;
        $this->directoryList = $directoryList;
        $this->fileFactory = $fileFactory;
        $this->uploader = $uploader;
        $this->state = $state;
        $this->scopeConfig = $scopeConfig;
        $this->eventManager = $eventManager;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->exportStatusRepository = $exportStatusRepository;
    }

    public function export($order)
    {
        $export = $this->exportRepository->create();

        $orders[] = $order;

        $converted = $this->orderCollectionConverter->toArray($orders);

        if ($convertedCount = count($converted)) {
            $exportType = $this->scopeConfig->getValue('orderexport/automatic/export_file_type');
            $exporter = $this->exporter->create($exportType);
            $result = $exporter->export($converted);
            $filename = $result['ordersData'][0]['filename'];
        } else {
            $filename = '';
            $result = ['success' => 0, 'successIds' => [], 'ordersData' => []];
        }

        $export->setResult('automatic', $filename, '', $result);
        $this->exportRepository->save($export);

        foreach ($result['ordersData'] as $order) {
            $this->uploader->upload($order['filepath']);
        }
        $this->eventManager->dispatch('cs_cron_orderexport_validate', ['result' => $result, 'export' => $export]);
    }

    public function schedule($order)
    {
        $date = date('Y-m-d H:i:s');
        $order->setExportScheduleDate($date);
        $this->orderRepositoryInterface->save($order);
    }
}