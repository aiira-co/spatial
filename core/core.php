<?php


class Core
{
    private static $instance = [];

    public function __construct()
    {
        $adConfig  = new AdConfig;
        $this->cors($adConfig->allow_origin, $adConfig->allow_methods, $adConfig->max_age);
    }
    // This Method is called in the root index.php file.
    // the Method intanciates the node class for routing
    public function route()
    {
        require_once 'core'.DS.'node.php';
        require_once 'core'.DS.'legacy.php';
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
                self::connectionString($class);
            } else {
                // $formatClass = ucfirst($class)
                self::$instance[$class] = new $class;
            }

            return self::$instance[$class];
        }
    }




    // Connection Iterator
    private static function connectionString($class)
    {
        $adConfig = new AdConfig;

        // check if the database values are Traversable or array
        if (!(is_array($adConfig->db) || ($adConfig->db instanceof Traversable))) {
            //Select the dbtype, whether mysql, mysqli,mssql, oracle, sqlite etc
            switch ($adConfig->dbtype) {
          case 'mysqli':

            self::$instance[$class] = new PDO("mysql:host = $adConfig->host;dbname=$adConfig->db", $adConfig->user, $adConfig->password);
            break;
          case 'oracle':
            self::$instance[$class] = new PDO("oci:dbname=".$adConfig->db, $adConfig->user, $adConfig->password);
            break;

          case 'mssql':
            self::$instance[$class] = new PDO("mssql:host = $adConfig->host;dbname=$adConfig->db", $adConfig->user, $adConfig->password);
            break;

          default:
            self::$instance[$class] = new PDO("mysql:host = $adConfig->host;dbname=$adConfig->db", $adConfig->user, $adConfig->password);
            break;
        }
        } else {
            //connection to multiple database
            $conType = 'mysql';
            switch ($adConfig->dbtype) {
          case 'mysqli':
            $conType = 'mysql';
            break;
          case 'oracle':
            $conType = 'oci';
            break;

          case 'mssql':
            $conType = 'mssql';
            break;

          default:
            $conType = 'mysql';
            break;
        }
            for ($i =0; $i < count($adConfig->db); $i++) {
                // echo $adConfig->pass[$i];
                $host = $adConfig->host[$i];
                $db = $adConfig->db[$i];
                self::$instance[$class][$i] = new PDO("$conType:host = $host;dbname=$db", $adConfig->user[$i], $adConfig->password[$i]);
            }
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



    // Automatically load required for to instantiate the class
    private static function autoload($class)
    {
        // echo memory_get_usage();
        $node = CORE::getInstance('Node'); //check to see if you can reduced memory usage here
        $paths = ['core','controllers'];
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

        if ($adConfig->content_type === 'xml') {
            // $xml = new SimpleXMLElement('<data/>');
            // array_walk_recursive(json_decode(json_encode($basket->result),true),[$xml,'addChild']);

            // echo $result;
            // self::arrayToXml($basket->result,$xml);
            // self::arrayToXml(['name'=>'ama'],$xml);

            // array_walk_recursive($result,[$xml,'addChild']);
            // print_r($basket->result);
            // echo $xml->asXML();
            echo xmlrpc_encode($basket->result);
        } else {
            echo json_encode($basket->result);
        }
    }


    private static function arrayToXml(array $data, SimpleXMLElement $xml_data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $xml_data->addChild($key);
                self::arrayToXml($value, $new_object);
            } else {
                if ($key === (int)$key) {
                    $key = "key_$key";
                }
                $xml_data->addChild($key, $value);
            }
        }
    }



    /**
     *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
     *  origin.
     *
     *  In a production environment, you probably want to be more restrictive, but this gives you
     *  the general idea of what is involved.  For the nitty-gritty low-down, read:
     *
     *  - https://developer.mozilla.org/en/HTTP_access_control
     *  - http://www.w3.org/TR/cors/
     *
     */
    public function cors($origin, $methods, $age)
    {

        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:

            $origin == '*' ? header("Access-Control-Allow-Origin:{$_SERVER['HTTP_ORIGIN']}") : header("Access-Control-Allow-Origin:".$origin) ;
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age:'.$age);    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods:".$methods);
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
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
