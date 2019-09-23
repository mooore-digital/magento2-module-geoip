<?php

declare(strict_types=1);

namespace Mooore\GeoIp\Api;

interface CountryCodeInterface
{
    /**
     * @return string
     */
    public function get(): string;
}
