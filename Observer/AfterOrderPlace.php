<?php
namespace MageSuite\SampleOrderExport\Observer;

class AfterOrderPlace extends \MageSuite\SampleOrderExport\Observer\AbstractOrderExportObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $status = [
            'status' => 'ready_to_generate',
            'completed' => '1'
        ];
        $this->exportStatusRepository->addStatus($observer->getOrder(), $status);

        $order = $observer->getOrder();
        $history = $order->addStatusHistoryComment('Order was placed and ready to generate export file.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();

        $status = [
            'status' => 'file_generated',
            'completed' => '1'
        ];
        $this->exportStatusRepository->addStatus($observer->getOrder(), $status);

        $order = $observer->getOrder();
        $history = $order->addStatusHistoryComment('Order export file was generated.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();
    }
}