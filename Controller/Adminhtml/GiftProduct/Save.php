<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Controller\Adminhtml\GiftProduct;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        Json $json
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->json          = $json;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('giftproduct_id');

            $model = $this->_objectManager->create(\Dimas\GiftProduct\Model\GiftProduct::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Giftproduct no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
//            $data['MainProduct'] = $this->json->serialize($data['MainProduct']);
//            $data['GiftProduct'] = $this->json->serialize($data['GiftProduct']);

//            $model->setStatus($data['Status']);
//            $model->setTitle($data['Title']);
            $data['MainProduct'] = implode(';', $data['MainProduct']);
            $data['GiftProduct'] = implode(';', $data['GiftProduct']);
//            $model->setMainProduct($data['MainProduct']);
//            $model->setGiftProduct($data['GiftProduct']);
//            $model->setTitle($data['Label']);
//            $model->setTitle($data['Qty']);

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Giftproduct.'));
                $this->dataPersistor->clear('dimas_giftproduct_giftproduct');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['giftproduct_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Giftproduct.'));
            }

            $this->dataPersistor->set('dimas_giftproduct_giftproduct', $data);
            return $resultRedirect->setPath('*/*/edit', ['giftproduct_id' => $this->getRequest()->getParam('giftproduct_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

