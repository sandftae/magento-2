<?php

namespace PleaseWork\Workshop\Controller\Adminhtml\Publisher;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use PleaseWork\Workshop\Model\PublisherRepository;

/**
 * Class InlineEdit
 * @package PleaseWork\Workshop\Controller\Adminhtml\Publisher
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var PublisherRepository
     */
    protected $publisherRepository;

    /**
     * InlineEdit constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param PublisherRepository $publisherRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PublisherRepository $publisherRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory          = $jsonFactory;
        $this->publisherRepository  = $publisherRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $publisherItems = $this->getRequest()->getParam('items', []);

        foreach ($publisherItems as $publisherData) {
            $publisher = $this->publisherRepository->getById($publisherData['publisher_id']);
            $publisher->setData($publisherData);
            try {
                $this->publisherRepository->save($publisher);
            } catch (\Exception $e) {
                $messages[] = __('Something went wrong while saving the page.');
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages'  => $messages,
            'error'     => $error
        ]);
    }
}
