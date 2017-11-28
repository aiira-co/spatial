<?php
// declare(strict_types=1);
class Legacy
{



    function set($key, $value)
    {
        $this->$key = $value;
    }

    function get($key)
    {
        return $this->$key ?? null;
    }
}








// Parent Class for Controllers

class CoreController
{
    private $router= [];
    private $routerExist = false;
    private $controller;

    private static $c = [];

    function __construct($controller, $router)
    {
        $this->router = $router;
        $this->controller = $controller;
        if (method_exists($controller, 'constructor')) {
            $controller->constructor();
        }
        // echo 'hello controller';
           $this->controllerRequest();
    }


    // The controller function must have an array as an argument not just variables
    function controllerRequest()
    {
        $controller = $this->controller;
        $basket = CORE::getInstance('basket');
        $legacy = CORE::getInstance('legacy');

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $httpRequest = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
        } else {
            $httpRequest = $_SERVER['REQUEST_METHOD'];
        }

        $httpMethod = 'http'.ucfirst(strtolower($httpRequest));
        // $basket->result['method']=$httpMethod;

        switch ($httpRequest) {
            case 'GET':
                # code...
                if (method_exists($controller, $httpMethod)) {
                    if (isset($legacy->params)) {
                        $basket->result = call_user_func_array(array($controller,$httpMethod), $legacy->params);
                    } else {
                        $basket->result = $controller->$httpMethod();
                    }
                } else {
                    $this->error($httpMethod);
                }
                break;


            case 'POST':
                $data  = file_get_contents('php://input');
                if (method_exists($controller, $httpMethod)) {
                    $basket->result = $controller->$httpMethod(json_decode($data, true));
                } else {
                    $this->error($httpMethod);
                }
                break;

            case 'PUT':
                $data  = file_get_contents('php://input');
                if (method_exists($controller, $httpMethod)) {
                    if (isset($legacy->params)) {
                        $basket->result = $controller->$httpMethod(json_decode($data, true), $legacy->params[1]);
                    } else {
                        $basket->result = $controller->$httpMethod(json_decode($data, true));
                    }
                } else {
                    $this->error($httpMethod);
                }
                break;

            case 'DELETE':
                if (method_exists($controller, $httpMethod)) {
                    if (isset($legacy->params)) {
                        $basket->result = call_user_func_array(array($controller,$httpMethod), $legacy->params);
                    } else {
                        $basket->result = $controller->$httpMethod();
                    }
                } else {
                      $this->error($httpMethod);
                }
                break;

            default:
            // echo 'default';
                $data  = file_get_contents('php://input');

                if (method_exists($controller, $httpMethod)) {
                    if (isset($legacy->params)) {
                        if (is_null($data)) {
                            $basket->result = call_user_func_array(array($controller,$httpMethod), $legacy->params);
                        } else {
                            $basket->result = $controller->$httpMethod(json_decode($data, true), $legacy->params[1]);
                        }
                    } else {
                        if (is_null($data)) {
                            $basket->result = $controller->$httpMethod();
                        } else {
                            $basket->result = $controller->$httpMethod(json_decode($data, true));
                        }
                    }
                } else {
                    // echo 'method doesnt exist';
                    $this->error($httpMethod);
                }
                break;
        }

        // it will now render at the CORE::render() called in the node.php file
    }



    function error(string $fx)
    {
        // echo 'error FXN'.$fx;
        $basket = CORE::getInstance('basket');
        $basket->result = ['error'=>'The Method '.$fx.'() is not declared in the contorller'];
    }
}






















// Parent Class for models



class CoreModel
{

    public static $pdo;
    public static $prefix;
    public static $sql;

    protected $data;
    public static $postId;
    protected $error;

    private static $bindParam = [];


    //A static variable to hold all values of the chain methods for use in
    // the createStatement() Method

    private static $s = [];


    //This method is used to store raw sql statement for query
    // Not tested yet
    public static function sql($sql): self
    {
        $this->dbSql = $sql ?? null;
        return new CoreModel;
    }


    //this Method is the first to be called for chaining.
    //  It sets the table to query and resets all the other methods to default

    // makes use of the table_exits method to check is the table is part of the DB
    // / it has a default alias of 't',
    // SELECT t.* FROM table t

    public static function table(string $table): self
    {
        self::$pdo = CORE::getInstance('pdo');

        if (!class_exists('AdConfig')) {
            require_once 'config.php';
        }
        self::$prefix  = (new AdConfig)->dbprefix;

        self::$s =[];

        $tables = self::tableExists($table);
        if ($tables) {
            // set tables
            self::$s['table'] = $tables['tables'];

            //set table alias
            if (!empty($tables['alias'])) {
                self::$s['alias'] = $tables['alias'];

              // print_r($tables['alias']);
            }
        } else {
            die('The Table '.self::$prefix.$table.' does not exists');
        }
        return new CoreModel;
    }





    //This Method is to set the fields of the table
    //SELECT 'fields' FROM ...
    //return self
    public function fields(string $fields): self
    {

        if (!isset(explode('.', $fields)[1])) {
            $fieldss ='';
            for ($i=0; $i<count(self::$s['table']); $i++) {
                $fieldss.= $this->fieldExists(self::$s['table'][$i], $fields, self::$s['alias'][$i]);
            }

            // echo $fieldss;
            self::$s['field'] = trim($fieldss, ',');
        } else {
            self::$s['field'] =  $fields;
        }

        return new CoreModel;
    }







    //This Method is to set wheres for the statement
    // i.e WhERE ...
    // returns CoreModel

    public function where(string $field, string $opValue, string $value = null): self
    {
        if ($field != null) {
            if (!isset(explode('.', $field)[1])) {
                for ($i = 0; $i < count(self::$s['table']); $i++) {
                    $fieldVerified = $this->fieldExists(self::$s['table'][$i], $field, self::$s['alias'][$i]);
                }

                $field = $fieldVerified;
            }

            if ($opValue == null) {
                echo 'fill second arg';
            } elseif ($opValue == "=" ||
                   $opValue == "!=" ||
                   $opValue == "<" ||
                   $opValue == ">" ||
                   $opValue == "<=" ||
                   $opValue == ">=" ||
                   $opValue == "BETWEEN" ||
                   $opValue == "IN" ||
                   $opValue == "NOT IN" ||
                   $opValue == "LIKE" ) {
                if ($value==null) {
                    echo 'fill third arg';
                } else {
                    self::$s['where'] = $field.' '.$opValue.' :'.str_replace('.', '_', $field);

                    self::$bindParam[':'.str_replace('.', '_', $field).''] = $value;
                }
            } else {
                self::$s['where'] = $field.' = :'.str_replace('.', '_', $field);
                self::$bindParam[':'.str_replace('.', '_', $field).''] = $opValue;
            }
        }

      // echo self::$dbWhere;

        return new CoreModel;
    }






    //This Method is to set wheres for the statement. used after the where() is called
    // i.e WhERE ... || ..
    // returns CoreModel
    public function orWhere(string $field, string $opValue, string $value = null): self
    {
      //checek if where is already set
        if (self::$s['where'] == null) {
            die('Call the method Where(\'id\',$id) before calling this method "orWhere()"');
        }

        if ($field != null) {
            if (!isset(explode('.', $field)[1])) {
                for ($i = 0; $i < count(self::$s['table']); $i++) {
                    $fieldVerified = $this->fieldExists(self::$s['table'][$i], $field, self::$s['alias'][$i]);
                }

                $field = $fieldVerified;
            }

            if ($opValue == null) {
                echo 'fill second arg';
            } elseif ($opValue == "=" ||
                   $opValue == "!=" ||
                   $opValue == "<" ||
                   $opValue == ">" ||
                   $opValue == "<=" ||
                   $opValue == ">=" ||
                   $opValue == "BETWEEN" ||
                   $opValue == "IN" ||
                   $opValue == "NOT IN" ||
                   $opValue == "LIKE" ) {
                if ($value==null) {
                    echo 'fill third arg';
                } else {
                    $OrWhere = $field.' '.$opValue.' :'.str_replace('.', '_', $field);
                    self::$s['where'] = self::$s['where'].' OR '.$OrWhere;
                    self::$bindParam[':'.str_replace('.', '_', $field).''] = $value;
                }
            } else {
                $OrWhere = $field.' = :'.str_replace('.', '_', $field);
                self::$s['where'] = self::$s['where'].' OR '.$OrWhere;
                self::$bindParam[':'.str_replace('.', '_', $field).''] = $opValue;
            }
        }

      // echo self::$dbWhere;

        return new CoreModel;
    }








    //This Method is to set wheres for the statement. used after the where() is called
    // i.e WhERE ... && ..
    // returns CoreModel

    public function andWhere(string $field, string $opValue, string $value = null): self
    {

        //checek if where is already set
        if (self::$s['where'] == null) {
            die('Call the method Where(\'id\',$id) before calling this method "andWhere()"');
        }
        if ($field != null) {
            if (!isset(explode('.', $field)[1])) {
                for ($i = 0; $i < count(self::$s['table']); $i++) {
                    $fieldVerified = $this->fieldExists(self::$s['table'][$i], $field, self::$s['alias'][$i]);
                }

                $field = $fieldVerified;
            }

            if ($opValue == null) {
                echo 'fill second arg';
            } elseif ($opValue == "=" ||
                   $opValue == "!=" ||
                   $opValue == "<" ||
                   $opValue == ">" ||
                   $opValue == "<=" ||
                   $opValue == ">=" ||
                   $opValue == "BETWEEN" ||
                   $opValue == "IN" ||
                   $opValue == "NOT IN" ||
                   $opValue == "LIKE" ) {
                if ($value==null) {
                    echo 'fill third arg';
                } else {
                    $AndWhere = $field.' '.$opValue.' :'.str_replace('.', '_', $field);
                    self::$s['where'] = self::$s['where'].' AND '.$AndWhere;
                    self::$bindParam[':'.str_replace('.', '_', $field).''] = $value;
                }
            } else {
                $AndWhere = $field.' = :'.str_replace('.', '_', $field);
                self::$s['where'] = self::$s['where'].' AND '.$AndWhere;
                self::$bindParam[':'.str_replace('.', '_', $field).''] = $opValue;
            }
        }

      // echo self::$dbWhere;

        return new CoreModel;
    }






    //This Method is used at the end of a chain to query the DB.
    //it returns an array of objects
    public function get() : ?array
    {
        $sql = $this->createStatement();
        return $this->query($sql);
    }





    // This Method counts the results of a query.
    // i.e SELECT COUNT(*) ...
    // returns an integer

    //Mostly use this method to chec is an item already exists and also for [pagination]
    public function count():int
    {
        self::$s['field'] = 'COUNT(*)';
        $sql = $this->createStatement();

        return json_decode(json_encode($this->query($sql)), true)[0]['COUNT(*)'];
    }



    //This Method is used to query distinct rows
    // i.e SELECT COUNT(*) ...
    //used at the end of a chain method. it automatically calls the get method
    public function distinct(): array
    {
        self::$s['field'] = 'DISTINCT '.self::$s['field'];
        return $this->get();
    }




    //This Method is to set LIMIT for the statement, taking the last ID
    // i.e ... LIMIT 1 ORDER BY id ACS
    // Hence it will limit it to one, order by ID
    function first()
    {
        self::$s['limit'] = 1;
        $sql = $this->createStatement();
        return $this->query($sql, false);
    }





    // This Method is no different from the first() on
    // i.e ... LIMIT 1 ORDER BY id ASC
    // returns an object
    function single()
    {
        return $this->first();
    }





    //This Method is to set LIMIT for the statement, taking the last ID
    // i.e ... LIMIT 1 ORDER BY id DESC
    // Hence it will limit it to one, order by ID
    // returns an object
    function last()
    {
        self::$s['limit'] = 1;
        $this->OrderBy('id');
        $sql = $this->createStatement();
        return $this->query($sql, false);
    }




    //This Method is to set LIMIT for the statement
  // i.e ... LIMIT 5
    // returns CoreModel for chaining methods
    function limit(int $limit): self
    {
        self::$s['limit'] = $limit;
        return new CoreModel;
    }





    //This Method is used to set OFFSET for the SQL statement
    // i.e ... OFFSET 5
    //returns objects. used for pagination
    function offset(int $n)
    {
        self::$s['offset'] = $n;
        if (!isset(self::$s['order'])) {
            $this->orderBy('id');
        }
        return $this->get();
    }





    //This Method sets the ORDER in which the queried data should display.
    // i.e ... ORDEY BY 'id' 'ASC'
    //returns CoreModel
    function orderBy(string $field, int $order = 1): self
    {

        for ($i = 0; $i < count(self::$s['table']); $i++) {
            $fieldVerified = $this->fieldExists(self::$s['table'][$i], $field, self::$s['alias'][$i]);
        }

        $field = $fieldVerified;

        if ($order==1) {
            $o = 'DESC';
        } elseif ($order == 2) {
            $o= 'ASC';
        } else {
            die('Please specify the parameter for the second argument <br/> 1 for DSC, 2 for ASC');
        }

        self::$s['order']= $field.' '.$o;
        return new CoreModel;
    }



    // GROUP BY
    //This Method is to group rows in a query.
    // best used in conjanction with count to get statistically data for graphs
    public function groupBy(string $fields): self
    {
        if (!isset(explode('.', $fields)[1])) {
            for ($i = 0; $i < count(self::$s['table']); $i++) {
                $fieldVerified = $this->fieldExists(self::$s['table'][$i], $fields, self::$s['alias'][$i]);
            }
            self::$s['groupBy'] = $fieldVerified;
        } else {
            self::$s['groupBy'] =  $fields;
        }
        return new CoreModel;
    }




    // Joining Tables
    // /INNER JOIN
    function join(string $table, string $alias):self
    {

        if (self::tableExists($table)) {
            $join = [' INNER JOIN '.self::$prefix.$table.' '.$alias];
            if (!isset(self::$s['joinTables'])) {
                self::$s['joinTables'] =[];
            }
            self::$s['joinTables'] = array_merge(self::$s['joinTables'], $join);
        } else {
            die('The Table '.$table.' does not exists');
        }

        return new CoreModel;
    }



    // /LEFT JOIN
    function leftJoin(string $table, string $alias):self
    {

        if (self::tableExists($table)) {
            if (!isset(self::$s['joinTables'])) {
                self::$s['joinTables'] =[];
            }
            $join = [' LEFT JOIN '.self::$prefix.$table.' '.$alias];
            self::$s['joinTables'] = array_merge(self::$s['joinTables'], $join);
        } else {
            die('The Table '.$table.' does not exists');
        }

        return new CoreModel;
    }






    // /RIGHT JOIN
    function rightJoin(string $table, string $alias):self
    {

        if (self::tableExists($table)) {
            if (!isset(self::$s['joinTables'])) {
                self::$s['joinTables'] =[];
            }
            $join = [' RIGHT JOIN '.self::$prefix.$table.' '.$alias];
            self::$s['joinTables'] = array_merge(self::$s['joinTables'], $join);
        } else {
            die('The Table '.$table.' does not exists');
        }

        return new CoreModel;
    }




    //Used after a join method to set the condition of the joint table
    //i.e JOIN 'table2' q ON t.q_id = q.id
    function on(string $jField, string $tField):self
    {
      // check if fields exists
        $on = [' ON '.$jField.' = '.$tField];
        if (!isset(self::$s['joinOn'])) {
            self::$s['joinOn'] =[];
        }
        self::$s['joinOn'] = array_merge(self::$s['joinOn'], $on);
        return new CoreModel;
    }









    //This Method is to ADD / INSERT row(s) of a table`
    //Last to be called at the end of a chain.

    function add(array $data):bool
    {

        $fields = array_keys($data);
        $length = count($fields);

        $field="";
        $values="";


        for ($i=0; $i < $length; $i++) {
            $field .=", `".$fields[$i]."`";

            $values .=", :".$fields[$i]."";
        }


        $field = trim($field, ',');
        $values = trim($values, ',');


        $sql = 'INSERT INTO '.self::$prefix;
        $sql .=explode(' ', self::genFieldsTables('tables'))[0].' (';
        $sql .= $field.') VALUES ('.$values.')';
      // echo $sql;

        for ($i=0; $i < $length; $i++) {
            self::$bindParam[':'.$fields[$i].''] = $data[$fields[$i]];
        }

      // print_r(self::$bindParam);

      // return true;
        self::$sql = $sql;



        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }
    }






    //This Method is to update row(s) of a table`
    //Last to be called at the end of a chain.
    //mostly used with where() to specify the id of the table to update.

    function update(array $data):bool
    {
        $fields = array_keys($data);

      // $basket->set("filds", $fields);

        $length = count($fields);

      // $basket->set("length", $length);

        $field="";
        $values="";
        for ($i=0; $i < $length; $i++) {
            $values .=", `".$fields[$i]."` = :".$fields[$i]."";
        }

        $values = trim($values, ',');


        $sql = 'UPDATE '.self::$prefix;
        $sql .=explode(' ', self::genFieldsTables('tables'))[0].' t SET '.$values;

        if (self::$s['where'] == null) {
            die('please specify data to delete. Call DB::Table(\'table\')->Where(\'id\',$id)->Update($arr)');
        }
        $sql .= (self::$s['where'] == null) ? '' :' WHERE '.self::$s['where'];

        self::$sql = $sql;


      // SET bindParam
        for ($i=0; $i < $length; $i++) {
            self::$bindParam[':'.$fields[$i].''] = $data[$fields[$i]];
        }
      // echo $sql;
      // print_r(self::$bindParam);


        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }
    }





    //This Method is to delete row(s) of a table`
    //Last to be called at the end of a chain.
    //mostly used with where() to specify the id of the table to delete.

    function delete():bool
    {
        $sql = 'DELETE FROM '.self::$prefix;
        $sql .= explode(' ', self::genFieldsTables('tables'))[0];
        if (self::$s['where'] == null) {
            die('please specify data to delete. Call DB::Table(\'table\')->Where(\'id\',$id)->Delete()');
        }

        $sql .= (self::$s['where'] == null) ? '' :' WHERE '.str_replace('t.', '', self::$s['where']);
      // echo self::$s['where'];

      // echo $sql;
        self::$sql = $sql;

        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }
    }



    private static function genFieldsTables(string $get):string
    {
      //table iteration
        $tables ="";
        $fields ="";
        if (isset(self::$s['table'])) {
            for ($i =0; $i < count(self::$s['table']); $i++) {
                $fields .= self::$s['alias'][$i].'.*,';
                $tables .= self::$s['table'][$i].' '.self::$s['alias'][$i].',';
            }
            // echo $tables;
        }

        $fields = self::$s['field']??trim($fields, ',');
        $tables = trim($tables, ',');
        if ($get =="fields") {
            $results = $fields;
        } else {
            $results = $tables;
        }

        return $results;
    }

    //This Method is used mostly in GET request to create or generate the
    // sql statement from the chain methods called in the model.
    // the get method then assigns this return to a variable for use in the query()
    // /returns a string
    private function createStatement():string
    {




      // $tableAlias = isset($alias)?'':' t';
      // $sql .=' FROM '.self::$prefix.self::$s['table'].$tableAlias;

        $sql = 'SELECT '.self::genFieldsTables('fields');

        $sql .= ' FROM '.self::genFieldsTables('tables');

      // Jion iteration


        if (isset(self::$s['joinTables'])) {
            for ($i =0; $i < count(self::$s['joinTables']); $i++) {
                $sql .= self::$s['joinTables'][$i].' '.self::$s['joinOn'][$i];
            }
        }

        $sql .= isset(self::$s['where']) ? ' WHERE '.self::$s['where'] : '';
        $sql .= isset(self::$s['groupBy']) ? ' GROUP BY '.self::$s['groupBy'] : '';
        $sql .= isset(self::$s['order']) ? ' ORDER BY '.self::$s['order'] : '';
        $sql .= isset(self::$s['limit']) ? ' LIMIT '.self::$s['limit'] : '';
        $sql .= isset(self::$s['offset']) ? ' OFFSET '.self::$s['offset'] : '';
        self::$sql = $sql; //want to have this static
    //   echo $sql;
        return $sql;
    }





    //This Method is to makes use of the PDO prepare method to query the statement
    // (from $this->createStatement(), $this->add(), $this->update() & $this->delete())
    // When a query is executed, we check to see if the execution was a GET, POST, PULL or DELETE.

    // If it was a GET, we find out if its limited to a single row to bass pdo->fetch, if Multiple
    // rows were queried, pdo->fetchAll is used

    // On the other hand, if its not a get request, we simply return a boolean to denote success or failure

    // A static variable $postId is altered if the request was a post. this way we can get the lastInsertId of the postId
    // to implement in other queries if neccessary

    private function query(string $sql, bool $fetchAll = true)
    {
        try {
            $query = self::$pdo->prepare($sql);

            // Use bindParam to prevent injection

              $fields = array_keys(self::$bindParam);
              $length = count($fields);

            for ($i=0; $i < $length; $i++) {
                $query->bindParam($fields[$i], self::$bindParam[$fields[$i]]);
            }


            //   print_r(self::$bindParam);
              //Empty bindParam;
              self::$bindParam = [];

            if ($query->execute()) {
                // If its a SELECT statment
                if ($sql[0]=='S') {
                  // $query->rowCount() > 1
                    return   $fetchAll ? $query->fetchAll(5): $query->fetch(5);
                } else {
                  // If its an INSERT statment
                    if ($sql[0]=='I') {
                        self::$postId = self::$pdo->lastInsertId();
                    }
                    return true;
                }
            } else {
                return null;
            }
        } catch (PDOExeption $e) {
            echo '[{"error":"'.$e->message().'"}]';
        }
    }






  //Method to check if a table exist,
  // if it does, it querys with it else it takes it out of the fields
    private static function tableExists($tables):array
    {
        //explode to see how many tables are being queried
        $tables = explode(',', trim($tables, ','));

        $length = count($tables);
        $tablesExist = [];
        $tableAlias =[];

        for ($i = 0; $i < $length; $i++) {
          // Now trim off any whitespaces
            $indexTable = trim($tables[$i], ' ');

          // Explode with DOT '.' to see if the databasename is attached to the table
            $indexTable = explode('.', $indexTable);
            if (isset($indexTable[1])) {
                $dbname= $indexTable[0].'.';
                $dbname='';
                $table = $indexTable[1];
            } else {
                $dbname="";
                $table = $indexTable[0];
            }

          //now see if th table already has an alias set to it, then remove it.
            $aliasTable = explode(' ', $table);
            if (isset($aliasTable[1])) {
                // alias exists
                $alias = $aliasTable[1];
                echo  $alias;
                $table = $aliasTable[0];
            } else {
                if ($length == 1) {
                    $alias = "t";
                } else {
                    $alias = "t".$i;
                }
            }

            if (self::$pdo->query("SHOW TABLES LIKE '".$dbname.self::$prefix.$table."'")->rowCount() == 1) {
                $tablesExist[$i]=$dbname.self::$prefix.$table;
                $tableAlias[$i] = $alias;
            } else {
                die('The Table '.$table.' does not exist in the database');
            }
        }


        return ['tables'=>$tablesExist,'alias'=>$tableAlias];
    }




    //Method to check is a field exist, if it does,
    //it querys with it else it takes it out of the fields

    private function fieldExists($table, $field, $alias):string
    {

        // echo 'table is: '.$table.' --- fields is: '.$field.' ---- alias is: '.$alias;
        $field = explode(',', trim($field, ','));

        $length = count($field);
        $exist = "";

        for ($i = 0; $i < $length; $i++) {
            // var_dump($field);
            
                                    
            if (self::$pdo->query("SHOW COLUMNS FROM ".self::$prefix.$table." LIKE '".$field[$i]."'") != null) {
                $exist .= ','.$alias.'.'.trim($field[$i], ' ');
            // echo $field[$i].'<br/>';
            }
        }


        return trim($exist, ',');
    }
}




















    //Sessions

class CoreSession
{

// ==================================================================
//
// User Login
//
// ------------------------------------------------------------------
    private static $user_id;
    private static $table;
    private static $emailField;
    private static $usernameField;
    private static $hashField;

    public static $error;


    // Method for initializing Session
    static function SessionInit($table = 'user', $emailField = 'email', $usernameField = 'username', $hashField = 'hashword')
    {
        self::$table = $table;
        self::$emailField = $emailField;
        self::$usernameField = $usernameField;
        self::$hashField = $hashField;
    }

    // Method to Login User
    public static function SessionLogin($uname, $umail, $upass):bool
    {

        try {
            $pdo = CORE::getInstance('pdo');

            if (self::$table =='') {
                 $this->SessionInit();
            }

            require_once 'config.php';
            $prefix  = (new AdConfig)->dbprefix;
            $sql = 'SELECT * FROM '.$prefix.self::$table.' WHERE ('.self::$usernameField.'=:uname || '.self::$emailField.'=:umail)  LIMIT 1';
            // echo $sql;
            $query = $pdo->prepare($sql);
            $query->execute([':uname'=>$uname, ':umail'=>$umail]);
            $userRow=$query->fetch(5);


            if ($query->rowCount() > 0) {
               // check if account is active
               // echo $userRow->acount_enabled;
                if ($userRow->account_enabled) {
                    // check is the accout is on lockout
                    if ($userRow->lockout_enabled) {
                        echo 'current time is '.strtotime(date('Y-m-d h:i:s')).' <br>time left is:: '.(strtotime(date('Y-m-d h:i:s')) - $userRow->lockout_end);
                        if ((strtotime(date('Y-m-d h:i:s')) - $userRow->lockout_end) > 0) {
                            echo '<br> changing lock here --> '. $userRow->lockout_enabled.'<br/>';
                            CoreModel::table(self::$table)
                            ->where('id', $userRow->id)
                            ->update(
                              [
                                'lockout_enabled' => false,
                                'lockout_end'=> 0
                              ]
                                  );

                            self::$error = '';
                            return false;
                        } else {
                            self::$error = 'locked';
                            return false;
                        }
                    } else {
                       // verify password
                        if (password_verify($upass, $userRow->{self::$hashField})) {
                            CoreModel::table(self::$table)
                            ->where('id', $userRow->id)
                            ->update(
                              [
                                'access_failed_count'=>0,
                                'lockout_enabled' => false ,
                                'lockout_end'=> 0
                              ]
                                  );

                              $_SESSION['user_session'] = $userRow->id;
                              $_SESSION['count'] = 0;
                              self::$user_id = $userRow->id;
                              return true;
                        } else {
                            self::$error = 'passwordError';
                            echo 'failed:: '.$userRow->access_failed_count;
                            if ($userRow->access_failed_count < 5) {
                                CoreModel::table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(array('access_failed_count'=>$userRow->access_failed_count + 1));
                            } elseif ($userRow->access_failed_count == 5) {
                                CoreModel::table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(
                                [
                                 'access_failed_count'=>$userRow->access_failed_count + 1,
                                 'lockout_enabled' => true ,
                                 'lockout_end'=> strtotime(date('Y-m-d h:i:s')) + 300
                                ]
                                     );
                            } elseif ($userRow->access_failed_count < 10) {
                                CoreModel::table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(['access_failed_count'=>$userRow->access_failed_count + 1]);
                            } elseif ($userRow->access_failed_count == 10) {
                                CoreModel::table(self::$table)
                                ->where('id', $userRow->id)
                                ->update(
                                [
                                  'account_enabled' => false,
                                  'access_failed_count'=>$userRow->access_failed_count + 1,
                                  'lockout_enabled' => true ,
                                  'lockout_end'=> strtotime(date('Y-m-d h:i:s')) + 86700
                                ]
                                     );
                            }
                            return false;
                        }
                    }
                } else {
                    self::$error = 'notActive';
                    return false;
                }
            } else {
                self::$error = 'notExist';
                return false;
            }
        } catch (PDOException $e) {
            echo 'its false';
            echo $e->getMessage();
        }
    }



// ==================================================================
//
// Check If User is logged in
//
// ------------------------------------------------------------------

    public static function IsLoggedIn():bool
    {
        if (isset($_SESSION['user_session'])) {
            return true;
        } else {
            return false;
        }
    }




    public function SessionMessage(string $msg = "")
    {
        if ($_SESSION['message'] =="") {
            $_SESSION['message'] = $msg;
        } else {
            return $_SESSION['message'];
        }
    }



// ==================================================================
//
// Logs User Out
//
// ------------------------------------------------------------------



    public function SessionLogout(): bool
    {
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }
}






















// this class is simply a filter between the component and the model
// They are effective in POST queries to strip tags and etc

class middleWare
{

    static function filterPost($default, array $post = null):array
    {
        // Set $post to default if its null
        $post = $post??$_POST;


        // store array keys or members for comparison
        $default = json_decode(json_encode($default), true);
        // print_r($default);

        $postKeys = array_keys($post);
        $defaultKeys = array_keys($default);

        // echo '<br/> post is';
        // print_r($postKeys);
        // echo '<br/> default is:';
        // print_r($defaultKeys);

        //count the numbers if members for the parameters

        $postCount = count($post);
        $defaultCount = count($default);

        // $update = [];

        for ($i =0; $i < $postCount; $i++) {
            // First check if the member exists,
            // if yes, compare values,
            //   if same, ignoire,
            //   if not the same value, add to a custom array,
            // if no, ignore

            // $update = array();

            if (in_array($postKeys[$i], $defaultKeys)) {
              // echo $postKeys[$i].' --> '.$post[$postKeys[$i]].'<br/>';
                $key =$postKeys[$i];
                $value = $post[$key];
                if ($post[$key] != $default[$key]) {
                    // echo 'changes found';

                    $update[$key] = $value;
                }
            }
        }

        return $update?? [];
    }
}
