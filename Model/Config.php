<?php

declare(strict_types=1);

namespace Mooore\GeoIp\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const CONFIG_PATH_IP_INFO_API_KEY = 'system/mooore_geoip/ipinfo_api_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getIpInfoApiKey(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_IP_INFO_API_KEY,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
