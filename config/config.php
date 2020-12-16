<?php

declare(strict_types=1);


use Presentation\AppModule;
use Spatial\Core\App;

/**
 * Configuration class for the API.
 * Toggle the $enableProdMode to show or hide CQured's error reporter.
 * Set Hearders & Corss Origin Settings.
 * Api's Router file path can be changed here too.
 */
class Config
{
    // General & PHP Doctrine Config
    public bool $enableProdMode = false;

    public string $appName = 'Spatial API';
    public array $offline = [
        'value' => false,
        'message' => 'This site is down for maintenance.<br />Please check back again soon.',
        'displayMessage' => '1',
        'image' => '',
    ];

    /**
     * @throws Exception
     */
    public function render(): void
    {
        $app = new App();
        try {
            $app->bootstrapModule(AppModule::class);
        } catch (ReflectionException $e) {
        }
    }

}
