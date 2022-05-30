<?php
namespace Dinarys\QuoteDownload\Controller\Cart\Download;

use Dinarys\QuoteDownload\Model\Cart\Converter\ToCsv as ConverterToCsv;
use Dinarys\QuoteDownload\Model\Config;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\Session as CustomerSession;

class Csv implements ActionInterface
{
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var ConverterToCsv
     */
    protected $converterToCsv;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Csv constructor.
     * @param ResultFactory $resultFactory
     * @param FileFactory $fileResponseFactory
     * @param ConverterToCsv $converterToCsv
     * @param ManagerInterface $messageManager
     * @param CustomerSession $customerSession
     * @param Config $config
     */
    public function __construct(
        ResultFactory $resultFactory,
        FileFactory $fileResponseFactory,
        ConverterToCsv $converterToCsv,
        ManagerInterface $messageManager,
        CustomerSession $customerSession,
        Config $config
    ) {
        $this->resultFactory = $resultFactory;
        $this->fileFactory = $fileResponseFactory;
        $this->converterToCsv = $converterToCsv;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->config->isEnabledCsvDownload() && $this->customerSession->isLoggedIn()) {
            try {
                $content = $this->converterToCsv->getFileContent();
                $fileName = $this->converterToCsv->getFileName();
                return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__("You are not logged in. Or download disabled."));
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
