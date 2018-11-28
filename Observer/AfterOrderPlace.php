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
            'label' => 'Order is ready to generate export file',
            'completed' => true
        ];
        $this->exportStatusRepository->addStatus($observer->getOrder(), $status);

        $order = $observer->getOrder();
        $history = $order->addStatusHistoryComment('Order was placed and ready to generate export file.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();
    }
}