<?php
namespace MageSuite\SampleOrderExport\Observer;

class AfterOrderSave extends \MageSuite\SampleOrderExport\Observer\AbstractOrderExportObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();

        $origData = $order->getOrigData();
        if($order->getState() == 'complete' && $origData['state'] != 'complete') {

            $status = [
                'status' => 'order_completed',
                'label' => 'Order is completed',
                'completed' => true
            ];
            $this->exportStatusRepository->addStatus($observer->getOrder(), $status);

            $order = $observer->getOrder();
            $history = $order->addStatusHistoryComment('Order is completed.');
            $history->setIsVisibleOnFront(false);
            $history->setIsCustomerNotified(false);
            $history->save();
        }
    }
}