<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Controller\Adminhtml\GiftProduct;

class Delete extends \Dimas\GiftProduct\Controller\Adminhtml\GiftProduct
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('giftproduct_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Dimas\GiftProduct\Model\GiftProduct::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Giftproduct.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['giftproduct_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Giftproduct to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

