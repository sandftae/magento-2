<?php

namespace PleaseWork\Workshop\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\AuthorizationInterface;

/**
 * Class Display
 * @package PleaseWork\Workshop\Controller\Adminhtml\Post
 */
class Display extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * Display constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        AuthorizationInterface $authorization
    ) {
        $this->pageFactory = $pageFactory;
        $this->authorization = $authorization;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->authorization->isAllowed('Magento_Customer::manage');
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }
}
