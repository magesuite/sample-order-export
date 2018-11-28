<?php
namespace MageSuite\SampleOrderExport\Observer;

class AfterCreateShipping extends \MageSuite\SampleOrderExport\Observer\AbstractOrderExportObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $status = [
            'status' => 'order_shipped',
            'completed' => '1'
        ];
        $this->exportStatusRepository->addStatus($observer->getShipment()->getOrder(), $status);

        $order = $observer->getShipment()->getOrder();
        $history = $order->addStatusHistoryComment('Order was shipped.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();
    }
}