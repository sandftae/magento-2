<?php

namespace PleaseWork\Workshop\Controller\Adminhtml\Publisher;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PleaseWork\Workshop\Model\PublisherRepository;

/**
 * Class Delete
 * @package PleaseWork\Workshop\Controller\Adminhtml\Publisher
 */
class Delete extends Action
{
    /**
     * @var PublisherRepository
     */
    protected $publisherRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param PublisherRepository $publisherRepository
     */
    public function __construct(
        Context $context,
        PublisherRepository $publisherRepository
    ) {
        $this->publisherRepository = $publisherRepository;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $publisherId = $this->getRequest()->getParam('publisher_id');

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $publisherItem = $this->publisherRepository->getById($publisherId);

            $this->publisherRepository->delete($publisherItem, $publisherId);

            $this->messageManager->addSuccessMessage('Hurray! The publisher has been delete ^_^.');

            return $resultRedirect->setPath('workshopmenu/publisher/display');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Oh! You have error: ' . $e->getMessage());

            return $resultRedirect->setPath('workshopmenu/publisher/display');
        }
    }
}
