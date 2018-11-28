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
            'completed' => '1'
        ];
        $this->exportStatusRepository->addStatus($observer->getInvoice()->getOrder(), $status);

        $order = $observer->getInvoice()->getOrder();
        $history = $order->addStatusHistoryComment('Order was payed.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();


        $status = [
            'status' => 'file_exported',
            'completed' => '1'
        ];
        $this->exportStatusRepository->addStatus($observer->getInvoice()->getOrder(), $status);

        $order = $observer->getInvoice()->getOrder();
        $history = $order->addStatusHistoryComment('Order export file was exported to ERP.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();

        $status = [
            'status' => 'file_processed_by_erp',
            'completed' => '1'
        ];
        $this->exportStatusRepository->addStatus($observer->getInvoice()->getOrder(), $status);

        $order = $observer->getInvoice()->getOrder();
        $history = $order->addStatusHistoryComment('Order export file was processed by ERP.');
        $history->setIsVisibleOnFront(false);
        $history->setIsCustomerNotified(false);
        $history->save();
    }
}