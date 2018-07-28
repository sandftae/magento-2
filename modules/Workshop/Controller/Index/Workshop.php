<?php

namespace PleaseWork\Workshop\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use PleaseWork\Workshop\Model\PostFactory;

/**
 * Class Workshop
 * @package PleaseWork\Workshop\Controller\GetStockConfig
 */
class Workshop extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * Workshop constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param PostFactory $postFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        PostFactory $postFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->postFactory = $postFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }
}
