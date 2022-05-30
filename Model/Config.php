<?php

namespace Encomage\QuoteDownload\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private const XML_PATH_DOWNLOAD_CSV_ENABLED = 'checkout/cart/download_csv_enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check is Customer can download quote via csv file on checkout cart page
     *
     * @return bool
     */
    public function isEnabledCsvDownload(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DOWNLOAD_CSV_ENABLED);
    }
}
