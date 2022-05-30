<?php

namespace Dinarys\QuoteDownload\Model\Cart\Converter;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Checkout\Model\Session;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

class ToCsv
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @var array
     */
    protected $allowedFields;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * ToCsv constructor.
     * @param Session $session
     * @param Filesystem $filesystem
     * @param array $allowedFields
     * @param string $fileName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Session $session,
        Filesystem $filesystem,
        $allowedFields = [],
        $fileName = ''
    ) {
        $this->session = $session;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->allowedFields = $allowedFields;
        $this->fileName = $fileName;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFileContent(): array
    {
        $quote = $this->getQuote();
        $this->directory->create('export');
        $fileName = 'export/quote-' . $quote->getId() . '.csv';
        $stream = $this->directory->openFile($fileName, 'w+');
        $stream->lock();
        $stream->writeCsv($this->allowedFields);

        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            $stream->writeCsv($this->getRowData($item));
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $fileName,
            'rm' => true
        ];
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param Item $item
     * @return array
     */
    protected function getRowData(Item $item): array
    {
        $row = [];
        foreach ($this->allowedFields as $field) {
            $row[$field] = $item->getData($field);
        }

        return $row;
    }

    /**
     * @return Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getQuote()
    {
        return $this->session->getQuote();
    }
}
