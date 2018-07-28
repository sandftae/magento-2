<?php

namespace PleaseWork\Workshop\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use PleaseWork\Workshop\Model\PostRepository;

/**
 * Class InlineEdit
 * @package PleaseWork\Workshop\Controller\Adminhtml\Post
 */
class InlineEdit extends Action
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
     * InlineEdit constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param PostRepository $postRepository
     */
    public function __construct(
        Context         $context,
        JsonFactory     $jsonFactory,
        PostRepository  $postRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory      = $jsonFactory;
        $this->postRepository   = $postRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);

        foreach ($postItems as $postData) {
            $post = $this->postRepository->getById($postData['post_id']);
            $post->setData($postData);
            try {
                $this->postRepository->save($post);
            } catch (\Exception $e) {
                $messages[] =  __('Something went wrong while saving the page.');
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
