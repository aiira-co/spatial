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
}
