<?php

namespace PleaseWork\Workshop\Controller\Adminhtml\Publisher;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PleaseWork\Workshop\Model\PublisherRepository;

/**
 * Class Save
 * @package PleaseWork\Workshop\Controller\Adminhtml\Publisher
 */
class Save extends Action
{
    /**
     * @var PublisherRepository
     */
    private $publisherRepository;

    /**
     * Save constructor.
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
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        if (empty($data['publisher_id'])) {
            $data['publisher_id'] = null;
            $newPublisher = $this->publisherRepository->createObject()->setData($data);
            try {
                $this->publisherRepository->save($newPublisher);
                $this->messageManager->addSuccessMessage('Hurray! The publisher has been add ^_^.');
            } catch (\Exception $e) {
                $this->messageManager->addSuccessMessage('Oh! You have error: ' . $e->getMessage());
                return $resultRedirect->setPath('workshopmenu/publisher/edit');
            }
        }

        $id = (int) $this->getRequest()->getPostValue('publisher_id');

        if ($id) {
            $publisher = $this->publisherRepository->getById($id);
            $publisher->setData($data);
            try {
                $this->publisherRepository->save($publisher);
                $this->messageManager->addSuccessMessage('Hurray! The publisher has been saved ^_^.');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('Oh! You have error: ' . $e->getMessage());
                return $resultRedirect->setPath('workshopmenu/publisher/edit');
            }
        }
        return $resultRedirect->setPath('workshopmenu/publisher/display');
    }
}