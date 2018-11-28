<?php
namespace MageSuite\SampleOrderExport\Observer;

class AfterCapturePayment extends \MageSuite\SampleOrderExport\Observer\AbstractOrderExportObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $status = [
            'status' => 'payment_created',
            'label' => 'Order was payed',
            'completed' => true
        ];
        $this->exportStatusRepository->addStatus($observer->getInvoice()->getOrder(), $status);

        $order = $observer->getInvoice()->getOrder();
        $history = $order->addStatusHistoryComment('Order was payed.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();
    }
}