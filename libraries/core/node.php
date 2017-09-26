<?php


class Node
{

    private $route = ['0'=>'aleph','1'=>'beth','2'=>'gimmel','3'=>'daleth','4'=>'hey'];
    public $router = [];
    private $adConfig;
    public $aleph;


    // Setters & Getters
    function set($key, $value)
    {
        $this->$key = $value;
    }

    function get($key)
    {
        return $this->$key ?? null;
    }


    function router()
    {
        $this->adConfig  =  new AdConfig;

        if (file_exists($this->adConfig->routerPath)) {
            $this->airRoute();
        } else {
            echo 'The file '.$this->adConfig->routerPath.'was not found at the specified destination <br><h2>Check the routerPath variable in config.php<h2>';
        }
    }



    private function airRoute()
    {
      // echo ' app router file exists';
        require_once $this->adConfig->routerPath;
        $coreRouter = CORE::getInstance('Router');
      // print_r($r->getRouter());

      // Get URL and Formate it
        $url = $_GET['url']??'/';
        $url = $url!='/' ? rtrim($url, '/'):'/';

        $routerPath = $coreRouter->getPath($url);
        $legacy = CORE::getInstance('Legacy');
      
      // echo 'hey';
      // print_r($routerPath);

        $this->adConfig  =  new AdConfig;

        $basket = CORE::getInstance('basket');
      // Check if url was found in the coreRouter
        if ($routerPath != null) {
            //Check if it has a redirect property
            if (isset($routerPath['redirectTo'])) {
                CORE::redirect($routerPath['redirectTo']);
            }



            //Check if it has a authentication property
            if (isset($routerPath['auth'])) {
                if ($routerPath['auth'][0]) {
                    $coreRouter->checkSession($routerPath['auth'][1], $routerPath['path']);
                }
            }



            // $corecontroller = new Corecontroller($path['controller'], $this->router);

            $this->aleph = $routerPath['controller'];
            $this->router[0] = $routerPath['controller'];

            $legacy->set('routerPath', $routerPath);
            $aleph = strtolower($this->aleph);
            // echo $aleph.'<br/>';
            $path = 'controllers'.DS.$aleph.'.controller.php';
            // echo $path;





            if (file_exists($path)) {
                // echo'file exists';
                require_once $path;
                $i = explode('-', $aleph);
                $class = isset($i[1]) ? ucfirst($i[0]).ucfirst($i[1]) : ucfirst($i[0]);
                        // $aleph;
                $class = $class.'Controller';
                // echo '<br/>'.$class;

                if (class_exists($class)) {
                    // echo 'got it';
                    $coreController = new CoreController(new $class, $this->router);
                } else {
                   
                    $basket->result = ['error'=>'The class '.$class.'does not exist. File: '.$path];
                }
            } else {
                $basket->result = ['error'=>'The controller path '.$path.' does not exist'];
            }
        } else {
             $basket->result = ['error'=>'The controller does not exist'];
            //  http_response_code (400);
             
        }

        CORE::render();
    }
}
