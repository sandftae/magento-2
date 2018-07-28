<?php

namespace PleaseWork\Workshop\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\TestFramework\Inspection\Exception;
use PleaseWork\Workshop\Model\PostRepository;

/**
 * Class Delete
 * @package PleaseWork\Workshop\Controller\Adminhtml\Post
 */
class Delete extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param PostRepository $postRepository
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PostRepository $postRepository
    ) {
        parent::__construct($context);

        $this->postRepository = $postRepository;
        $this->jsonFactory    = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postId = $this->getRequest()->getParam('post_id');

        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $postItem = $this->postRepository->getById($postId);

            $this->postRepository->delete($postItem, $postId);

            $this->messageManager->addSuccessMessage('Hurray! The post has been delete ^_^.');

            return $resultRedirect->setPath('workshopmenu/post/display');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Oh! You have error: ' . $e->getMessage());

            return $resultRedirect->setPath('workshopmenu/post/display');
        }
    }
}
