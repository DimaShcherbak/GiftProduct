<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Controller\Adminhtml\GiftProduct;

use Magento\Framework\Serialize\Serializer\Json;

class Edit extends \Dimas\GiftProduct\Controller\Adminhtml\GiftProduct
{
    /**
     * @var Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Json $json
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->json              = $json;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('giftproduct_id');
        $model = $this->_objectManager->create(\Dimas\GiftProduct\Model\GiftProduct::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
//            $model['MainProduct'] = $this->json->unserialize($model->getData('MainProduct'));
//            $model['GiftProduct'] = $this->json->unserialize($model->getData('GiftProduct'));

            $model['MainProduct'] = explode(';',$model->getData('MainProduct'));
            $model['GiftProduct'] = explode(';',$model->getData('GiftProduct'));

            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Giftproduct no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('dimas_giftproduct_giftproduct', $model);

        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Giftproduct') : __('New Giftproduct'),
            $id ? __('Edit Giftproduct') : __('New Giftproduct')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Giftproducts'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Giftproduct %1', $model->getId()) : __('New Giftproduct'));
        return $resultPage;
    }
}

