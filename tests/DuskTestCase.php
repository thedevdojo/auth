<?php

namespace Tests;

use AleBatistella\DuskApiConf\Traits\UsesDuskApiConfig;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    use UsesDuskApiConfig;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    public function findLineContainingSubstring($content, $substring)
    {
        $lines = explode("\n", $content);
        $foundLine = current(array_filter($lines, fn ($line) => strpos($line, $substring) === 0));

        return $foundLine ?: null;
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $chromeOptions = [
            '--disable-gpu',
            '--window-size=1920,1080',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-software-rasterizer',
        ];

        if (env('APP_ENV') != 'local') {
            $chromeOptions[] = '--headless';
        }

        $options = (new ChromeOptions)->addArguments($chromeOptions);

        $options->setExperimentalOption('mobileEmulation', ['userAgent' => 'laravel/dusk']);

        return RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
