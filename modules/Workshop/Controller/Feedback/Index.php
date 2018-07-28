<?php

namespace PleaseWork\Workshop\Controller\Feedback;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\App\Action\Action;
use \Magento\Framework\View\Result\PageFactory;
use PleaseWork\Workshop\Api\Data\FeedBackInterface;

class Index extends Action implements FeedBackInterface
{
    protected $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        return $this->pageFactory->create();
    }

    public function prepareToSave()
    {
        // TODO: Implement prepareToSave() method.
    }

}
