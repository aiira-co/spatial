<?php

/**
 * Copyright (c) 2018 ProjectAIIR.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     [ ProjectAIIR ]
 * @subpackage  [ cqured ]
 * @author      Owusu-Afriyie Kofi <koathecedi@gmail.com>
 * @copyright   2018 ProjectAIIR.
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://airDesign.co.nf
 * @version     @@2.00@@
 */

// declare(strict_types=1);

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day

}
// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
}

/**
 * Bootstrap the app
 */
class Startup
{
    private Config $webConfig;

    public function __construct()
    {
        // echo  $_SERVER['REQUEST_URI'];

        define('DS', DIRECTORY_SEPARATOR);
        require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';
        require_once __DIR__ . DS . '..' . DS . 'config' . DS . 'config.php';

        $this->webConfig = new Config();

        if ($this->webConfig->offline['value']) {
            try {
                echo json_encode(
                    ['notify' => 'success', 'result' => $this->webConfig->offline['message']],
                    JSON_THROW_ON_ERROR,
                    512
                );
            } catch (JsonException $e) {
                echo $e->getMessage();
            }
        } else {
            $this->_bootstrapApp();
        }
    }

    /**
     *
     */
    private function _bootstrapApp(): void
    {
//        init params
        $configDir = '..' . DS . 'config' . DS;

        try {
//    config/service.yml
            $services = Yaml::parseFile($configDir . 'services.yaml');
            define('SpatialServices', $services['parameters']);
//    config/packages/doctrine.yaml
            $doctrineConfigs = Yaml::parseFile($configDir . DS . 'packages' . DS . 'doctrine.yaml');
            define('DoctrineConfig', $doctrineConfigs);
        } catch (ParseException $exception) {
            printf('Unable to parse the YAML string: %s', $exception->getMessage());
        }

//        app render

        $this->webConfig->render();
    }
}


$app = new Startup();
