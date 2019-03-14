<?php

namespace Sandftae\CustomShipping\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Sandftae\CustomShipping\Api\ExportInterface;
use Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\Message\ManagerInterface;

/**
 * Class Export
 *
 * @package Sandftae\CustomShipping\Controller\Adminhtml\Export
 */
class Export extends Action implements HttpPostActionInterface, HttpGetActionInterface, CsrfAwareActionInterface
{
    /**
     * @var ExportInterface
     */
    protected $export;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Export constructor.
     *
     * @param Context           $context
     * @param ExportInterface   $export
     * @param ResultFactory     $resultFactory
     * @param ManagerInterface  $messageManager
     */
    public function __construct(
        Context             $context,
        ExportInterface     $export,
        ResultFactory       $resultFactory,
        ManagerInterface    $messageManager
    ) {
        $this->export           = $export;
        $this->resultFactory    = $resultFactory;
        $this->messageManager   = $messageManager;
        parent::__construct($context);
    }

    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // if the given is not in the database - cancel the execution
        if (empty($this->export->getData())) {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            return $resultJson->setData(['status' => true]);
        }
        return  $this->export->createCsvFile()->setData()->getCsvFile();
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
     * @param   RequestInterface $request
     * @return  bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
