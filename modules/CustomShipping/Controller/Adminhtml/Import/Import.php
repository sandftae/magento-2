<?php

namespace Sandftae\CustomShipping\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Sandftae\CustomShipping\Api\ImportInterface;

/**
 * Class Import
 *
 * @package Sandftae\CustomShipping\Controller\Adminhtml\Import
 */
class Import extends Action implements HttpPostActionInterface, HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ImportInterface
     */
    protected $importModel;

    /**
     * Import constructor.
     *
     * @param Context           $context
     * @param ResultFactory     $resultFactory
     * @param ImportInterface   $importModel
     */
    public function __construct(
        Context             $context,
        ResultFactory       $resultFactory,
        ImportInterface     $importModel
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->importModel   = $importModel;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $fileGlobal = $this->getRequest()->getFiles();
        $simpleDataFile = $fileGlobal->get('file');

        // if the user does not load the CSV - stop the execution of the controller
        if (is_null($simpleDataFile)) {
            return;
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $this->importModel->validate($simpleDataFile);

            $resultJson->setData([
                'status'    => true,
            ]);
        } catch (\Exception $e) {
            $resultJson->setData([
                'status'    => false,
                'message'   => $this->importModel->setMessage()
            ]);
        }
        return $resultJson;
    }

    /**
     * If you want to verify the query yourself, you need to do
     * this in this method. This is an interface method.
     * If not - return null.
     *
     * @param   RequestInterface                $request
     * @return  InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Validation of the client who applied to this the controller.
     *
     * @param   RequestInterface    $request
     * @return  bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
