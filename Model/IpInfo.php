<?php

declare(strict_types=1);

namespace Mooore\GeoIp\Model;

use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class IpInfo implements WebserviceInterface
{
    /**
     * @var ClientFactory
     */
    private $clientFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Json
     */
    private $jsonSerializer;
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        ClientFactory $clientFactory,
        LoggerInterface $logger,
        Json $jsonSerializer,
        Config $config
    ) {
        $this->clientFactory = $clientFactory;
        $this->logger = $logger;
        $this->jsonSerializer = $jsonSerializer;
        $this->config = $config;
    }

    /**
     * Get country code by ipinfo.io webservice.
     *
     * @see https://ipinfo.io/developers
     * @param string $remoteAddress
     * @return string
     */
    public function getCountryCode(string $remoteAddress): string
    {
        if (empty($this->config->getIpInfoApiKey())) {
            return '';
        }

        $httpClient = $this->clientFactory->create();
        $httpClient->addHeader('Authorization', sprintf('Bearer %s', $this->config->getIpInfoApiKey()));
        $httpClient->addHeader('Accept', 'application/json');
        $httpClient->setTimeout(2);

        $loggingContext = [
            'service' => 'geoip_ipinfo',
            'remote_address' => $remoteAddress,
        ];

        try {
            $httpClient->get(sprintf('https://ipinfo.io/%s', $remoteAddress));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $loggingContext);
            return '';
        }

        /*
         * Check if we reached the rate limit.
         * - https://ipinfo.io/developers#rate-limits
         */
        if ($httpClient->getStatus() === 429) {
            $this->logger->warning('Too many requests', $loggingContext);
            return '';
        }

        $data = $this->jsonSerializer->unserialize((string) $httpClient->getBody());
        if ((array_key_exists('bogon', $data) && $data['bogon']) || empty($data['country'])) {
            return '';
        }

        return $data['country'];
    }
}
