<?php

namespace PleaseWork\Workshop\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use PleaseWork\Workshop\Api\Data\PostInterface;
use PleaseWork\Workshop\Model\PostFactory;
use PleaseWork\Workshop\Model\PostRepository;

/**
 * Class Display
 * @package PleaseWork\Workshop\Block
 */
class Display extends Template
{
    /**
     * @var PostFactory
     */
    public $postFactory;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * Display constructor.
     * @param Context $context
     * @param PostFactory $postFactory
     * @param PostInterface $postRepository
     */
    public function __construct(
        Context $context,
        PostFactory $postFactory,
        PostInterface $postRepository
    ) {
        $this->postRepository   = $postRepository;
        $this->postFactory      = $postFactory;

        parent::__construct($context);
    }


    /**
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save()
    {
        $post = $this->postFactory->create();
        $post->setName('TEST');
        $post->setUrlKey('TEST');
        $post->setPostContent('TEST');
        $post->setTags('TEST');
        $post->setStatus('TEST');
        $post->setFeaturedImage('TEST');
        $this->postRepository->save($post);
    }

    /**
     * test method
     *
     * @return \Magento\Framework\Phrase
     */
    public function sayHello()
    {
        return __('Hello world Magento 2! 
                   This is a the block Display.php
                   and hes a method sayHello()');
    }

    /**
     * @return mixed|\PleaseWork\Workshop\Model\ResourceModel\Post\Collection
     */
    public function getPostCollection()
    {
        return $this->postRepository->getList();
    }

    /**
     *
     * @param $id
     * @return mixed|PostRepository
     */
    public function getById($id)
    {
        return $this->postRepository->getById($id);
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function delete()
    {
        $this->postRepository->delete($this->postFactory, 3);
    }

    public function getFilterList()
    {
        return $this->postRepository->getFilterList();
    }
}
