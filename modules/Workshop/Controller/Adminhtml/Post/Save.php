<?php

namespace PleaseWork\Workshop\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PleaseWork\Workshop\Model\PostRepository;

/**
 * Class Send
 * @package PleaseWork\Workshop\Controller\Adminhtml\Post
 */
class Save extends Action
{
    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * Send constructor.
     * @param Context $context
     * @param PostRepository $postRepository
     */
    public function __construct(
        Context $context,
        PostRepository $postRepository
    ) {
        $this->postRepository   = $postRepository;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if (empty($data['post_id'])) {
            $data['post_id'] = null;
            $newPost = $this->postRepository->createObject()->setData($data);
            try {
                $this->postRepository->save($newPost);
                $this->messageManager->addSuccessMessage('Hurray! The post has been add ^_^.');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('Oh! You have error: ' . $e->getMessage());
                return $resultRedirect->setPath('workshopmenu/post/edit');
            }
        }

            $id = (int) $this->getRequest()->getPostValue('post_id');

        if ($id) {
            $post = $this->postRepository->getById($id);
            $post->setData($data);
            try {
                $post = $this->postRepository->getById($id);
                $post->setData($data);
                $this->postRepository->save($post);
                $this->messageManager->addSuccessMessage('Hurray! The post has been saved ^_^.');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('Oh! You have error: ' . $e->getMessage());
                return $resultRedirect->setPath('workshopmenu/post/edit');
            }
        }
        return $resultRedirect->setPath('workshopmenu/post/display');
    }
}
