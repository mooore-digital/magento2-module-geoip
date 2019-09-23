<?php

declare(strict_types=1);

namespace Mooore\GeoIp\Model;

use Mooore\GeoIp\Api\CountryCodeInterface;

class CountryCode implements CountryCodeInterface
{
    /**
     * Remote address env variable
     */
    const ENV_REMOTE_ADDRESS = 'REMOTE_ADDR';
    /**
     * Country code env variable from GeoIp
     */
    const ENV_GEOIP_COUNTRY_CODE = 'GEOIP_COUNTRY_CODE';
    /**
     * Country code env variable from GeoIp2
     */
    const ENV_GEOIP2_COUNTRY_CODE = 'COUNTRY_CODE';
    /**
     * Country code header from CloudFlare
     */
    const ENV_CLOUDFLARE_COUNTRY_CODE = 'HTTP_CF_IPCOUNTRY';

    /**
     * @var WebserviceInterface
     */
    private $webservice;

    public function __construct(WebserviceInterface $webservice)
    {
        $this->webservice = $webservice;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->geoip() ?: $this->geoip2() ?: $this->cloudflare() ?: $this->webservice();
    }

    /**
     * Get GeoIp country code.
     *
     * @see http://nginx.org/en/docs/http/ngx_http_geoip_module.html
     * @return string
     */
    private function geoip(): string
    {
        return (string) getenv(self::ENV_GEOIP_COUNTRY_CODE) ?: '';
    }

    /**
     * Get GeoIp2 country code.
     *
     * @see https://github.com/leev/ngx_http_geoip2_module
     * @return string
     */
    private function geoip2(): string
    {
        return (string) getenv(self::ENV_GEOIP2_COUNTRY_CODE) ?: '';
    }

    /**
     * Get CloudFlare country code.
     *
     * @see https://support.cloudflare.com/hc/en-us/articles/200168236-What-does-Cloudflare-IP-Geolocation-do-
     * @return string
     */
    private function cloudflare(): string
    {
        return (string) getenv(self::ENV_CLOUDFLARE_COUNTRY_CODE) ?: '';
    }

    private function webservice(): string
    {
        return $this->webservice->getCountryCode(
            $this->getRemoteAddress()
        );
    }

    /**
     * Get IP address.
     *
     * @return string
     */
    private function getRemoteAddress(): string
    {
        return getenv(self::ENV_REMOTE_ADDRESS);
    }
}
