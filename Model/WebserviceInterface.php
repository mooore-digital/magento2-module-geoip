<?php

declare(strict_types=1);

namespace Mooore\GeoIp\Model;

interface WebserviceInterface
{
    public function getCountryCode(string $remoteAddress): string;
}
