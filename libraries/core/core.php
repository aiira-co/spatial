<?php


class Core
{


    private static $instance = [];

    function __construct()
    {
        $adConfig = new AdConfig;
      // check if live_site is ot empty
        if (!empty($adConfig->live_site)) {
            $baseUrl = $adConfig->live_site;
        } else {
            // /check if its a secured connection
            $http =isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
            $serverName = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];

            // echo 'Server Name'.$serverName.'<br>';

            // attach directory to the serverName if its not public
            $dir = explode(DS, getcwd());
            $countDir = count($dir);

            $dir = $dir[$countDir - 1];

            $rootPaths = ['htdocs','www'];
            for ($i=0; $i < count($rootPaths); $i++) {
                if ($dir != $rootPaths[$i]) {
                    $is_root = false;
                } else {
                    $is_root = true;
                }
            }
            if ($is_root) {
                $baseUrl = $serverName;
            } else {
                if ($serverName == $dir) {
                    $baseUrl = $http.$serverName.'/';
                } else {
                    $baseUrl = $http.$serverName.'/'.$dir.'/';
                }
            }
        }

        define('BaseUrl', $baseUrl);
    }
    // This Method is called in the root index.php file.
    // the Method intanciates the node class for routing
    function route()
    {

        require_once 'libraries'.DS.'core'.DS.'node.php';
        require_once 'libraries'.DS.'core'.DS.'legacy.php';
        $node = CORE::getInstance('Node');
        $node->router();
        // $this->aleph = $node->aleph;
    }




    // Method to Instantiate a class.
    // It first checks to see if class is available
    // if yes, return the instance of the class
    //   hence [new className]
    // if class is not available, require the class file, then repeat the
    // process again to instantiate

    public static function getInstance($class)
    {


        // check if instance of the class already exist
        if (isset(self::$instance[$class])) {
            return self::$instance[$class];
        } else {
          // check if class is available
            if (!class_exists($class)) {
                self::Autoload($class);
            }

            // if the argument entered is PDO, then return the Database connection
            if ($class == 'pdo') {
                        $adConfig = new AdConfig;

                        //Select the dbtype, whether mysql, mysqli,mssql, oracle, sqlite etc
                if ($adConfig->dbtype == "mysqli" || $adConfig->dbtype == "mysql") {
                    self::$instance[$class] = new PDO("mysql:host = $adConfig->host;dbname=$adConfig->db", $adConfig->user, $adConfig->password);
                } elseif ($config->server == "oracle") {
                              self::$instance[$class] = new PDO("oci:dbname=".$adConfig->db, $adConfig->user, $adConfig->password);
                } elseif ($config->server == "mssql") {
                    self::$instance[$class] = new PDO("mssql:host = $adConfig->host;dbname=$adConfig->db", $adConfig->user, $adConfig->password);
                }
            } else {
                // $formatClass = ucfirst($class)
                self::$instance[$class] = new $class;
            }

            return self::$instance[$class];
        }
    }




    // Instantiate a Model
    // This class requires attention
    // First path must be 'components/model/[name].model.php'
    //Also set a second parameter for it to check the path

    public static function getModel($model, $path = null)
    {

            $file = $path??'models'.DS.$model .'.model.php';

            $class = ucfirst($model).'Model';
        if (class_exists($class)) {
            return new $class;
        } else {
            if (file_exists($file)) {
                require_once $file;
                if (class_exists($class)) {
                    return new $class;
                } else {
                    die('The Class '.$class.' does not exist in the file '.$file);
                }
            } else {
                die('The Model Path '.$file.' Was Not FOUND!!');
            }
        }
    }


    // Instantiate a Plugin
    //I have never used this plugin before. Work on it.
    public static function getPlugin($plugin)
    {
        require_once 'plugins/index.php';
        $plugins = new Plugins($plugin);


        if (isset($plugins->$plugin)) {
            $plugin = $plugins->$plugin;
          // print_r($plugin);
            $file = $plugin['path'];
            $class = $plugin['class'];

            if (file_exists($file)) {
                require_once $file;

                if (class_exists($class)) {
                    return new $class;
                } else {
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }

      //  $plugins;
    }





    // Automatically load required for to instatiate the class
    private static function autoload($class)
    {
      // echo memory_get_usage();
        $node = CORE::getInstance('Node'); //check to see if you can reduced memory usage here
        $paths = ['libraries'.DS.'core','controllers'];
        foreach ($paths as $path) {
            $file = $path.DS.strtolower($class).'.php';

            // echo '<p>'.$file.'</p>';

            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
    }





    // Renders
    // This method takes the view and component obj for rendering.
    // it sets them for the CoreApp Method which is ad only declared in the
    // workspace template where the component will be seen in the UI of the app or
    // website.

    public static function render()
    {

        $adConfig = new AdConfig;

        $basket = CORE::getInstance('basket');
        $params = CORE::getInstance('params');

        // instead of params, use the header's application to see the return
        //also check for password if it matches.

        if (($params->get('api')) == 'json' && ($params->get('hash')) == ($adConfig->secret)) {
            echo json_encode($basket->result);
        } else {
            echo json_encode($basket->result);
        }
    }



  // Method for redirecting.
  // This method is also used in the node class for redirecting routes

    public static function redirect($url, $redirectTo = false, $code = 302)
    {
        $adConfig = new AdConfig;

        if ($redirectTo) {
            $airJaxURL = '&api=airJax';
            $url = $adConfig->airJax ? $url.$redirectTo.$airJaxURL : $url.$redirectTo;
        } else {
            $airJaxURL = '?api=airJax';
            $url = $adConfig->airJax ? $url.$airJaxURL : $url;
        }


        if (strncmp('cli', PHP_SAPI, 3) !== 0) {
            if (!headers_sent()) {
                if (strlen(session_id()) > 0) { // if using sessions
                    session_regenerate_id(true); // avoids session fixation attacks
                    session_write_close(); // avoids having sessions lock other requests
                }

                if (strncmp('cgi', PHP_SAPI, 3) === 0) {
                    header(sprintf('Status: %03u', $code), true, $code);
                }

                header('Location: ' . $url, true, (preg_match('~^30[1237]$~', $code) > 0) ? $code : 302);
            } else {
                echo "<meta http-equiv=\"refresh\" content=\"0;url=$url\">\r\n";
            }

            exit();
        }
    }
}
