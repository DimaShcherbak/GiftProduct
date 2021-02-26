<?php
declare(strict_types=1);

namespace Dimas\GiftProduct\Controller\Adminhtml;

abstract class GiftProduct extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Dimas_GiftProduct::top_level';
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Dimas'), __('Dimas'))
            ->addBreadcrumb(__('Giftproduct'), __('Giftproduct'));
        return $resultPage;
    }
}

