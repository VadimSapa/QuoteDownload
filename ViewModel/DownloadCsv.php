<?php

namespace Encomage\QuoteDownload\ViewModel;

use Encomage\QuoteDownload\Model\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context;
use Magento\Framework\UrlInterface;

class DownloadCsv implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * DownloadCsv constructor.
     * @param UrlInterface $urlBuilder
     * @param Config $config
     * @param HttpContext $httpContext
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Config $config,
        HttpContext $httpContext
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
        $this->httpContext = $httpContext;
    }

    /**
     * Download Url
     *
     * @return string
     */
    public function getDownloadUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout/cart/download_csv');
    }

    /**
     * Retrieve true if a customer is logged in and downloading is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isEnabledCsvDownload() && $this->isLoggedIn();
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    private function isLoggedIn(): bool
    {
        return (bool)$this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}
