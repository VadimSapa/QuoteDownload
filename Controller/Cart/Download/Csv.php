<?php
namespace Encomage\QuoteDownload\Controller\Cart\Download;

use Encomage\QuoteDownload\Model\Cart\Converter\ToCsv as ConverterToCsv;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;

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
     * Csv constructor.
     * @param ResultFactory $resultFactory
     * @param FileFactory $fileResponseFactory
     * @param ConverterToCsv $converterToCsv
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ResultFactory $resultFactory,
        FileFactory $fileResponseFactory,
        ConverterToCsv $converterToCsv,
        ManagerInterface $messageManager
    ) {
        $this->resultFactory = $resultFactory;
        $this->fileFactory = $fileResponseFactory;
        $this->converterToCsv = $converterToCsv;
        $this->messageManager = $messageManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $content = $this->converterToCsv->getFileContent();
            $fileName = $this->converterToCsv->getFileName();
            return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
