<?php
declare(strict_types=1);

namespace Wizacha\ApmBundle;

use PhilKra\Agent;

class PhilkraAgentFactory
{
    /** @var string */
    private $appName;

    /** @var string */
    private $appVersion;

    /** @var string */
    private $environment;

    /** @var string */
    private $serverUrl;

    /** @var string */
    private $secretToken;

    public function createPhilkraAgent(
        string $appName,
        string $appVersion,
        string $environment,
        string $serverUrl,
        string $secretToken
    )
    {
        $serverUrl = rtrim ($serverUrl, "/");

        $philkraAgent = new Agent([
            'appName' => $appName,
            'appVersion' => $appVersion,
            'environment' => $environment,
            'serverUrl' => $serverUrl,
            'secretToken' => $secretToken,
        ]);

        return $philkraAgent;
    }
}
