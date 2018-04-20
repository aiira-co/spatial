 <?php

 class Router
 {
     private $routers = [];
     private $path = [];

     public function setRouter(array $r)
     {
         $this->routers = $r;
     }

     public function getRouter():array
     {
         return $this->routers;
     }

     public function getPath(string $url)
     {
         $gen = $this->pathGen();
         $gen->send($url);
         $val = $gen->current();
         if ($val) {
             // echo $val;
             // echo $gen->getReturn();
             // echo 'path is found at text ONE';
             return $this->path;
         }

         $gen->next();

         $val = $gen->current();


         if ($val) {
             //  echo $val;
             //  echo $gen->getReturn();
             //  echo 'path is found at text TWO';
             return $this->path;

             $gen->next();
             $val = $gen->current();

             if ($val) {
                 // echo $val;
                 // echo $gen->getReturn();
                 // echo 'path is found at text THREE';
                 return $this->path;
             } else {
                 return null;
             }
         } else {
             return null;
         }
     }



     public function pathGen()
     {

              // /case ONE
         $path = [];
         $found = false;


         // get the url
         $url = yield;


         for ($i = 0; $i < count($this->routers); $i++) {
             // echo $this->routers[$i]['path'].' </br>';

             if ($this->routers[$i]['path'] == $url) {
                 $this->path =  $this->routers[$i];
                 $path = $this->path;
                 $found = true;
             }
         }



         yield $found;




         // case 2
         $url = explode('/', $url);
         // print_r($url);

         // echo json_encode($url);

         for ($i = 0; $i < count($this->routers); $i++) {
             // echo $this->routers[$i]['path'].' </br>';
             $pathX = explode('/', $this->routers[$i]['path']);
             //get count of both pathx and url
             $countUrl = count($url);
             $countPathX = count($pathX);

             if ($countPathX < $countUrl) {
                 $offset = $countUrl - $countPathX;

                 $comparableUrl ='';
                 $comparablePathX ='';
                 $urlParams = $url;

                 for ($j=0;$j<($countUrl - $offset);$j++) {
                     $comparableUrl .=$url[$j].'/';
                     $comparablePathX .=$pathX[$j].'/';

                     unset($urlParams[$j]);
                 }
                 // echo json_encode(['url timed'=>$comparableUrl,'path'=>$comparablePathX, 'params'=>$urlParams]);
                 if (trim($comparableUrl) == trim($comparablePathX)) {
                     // echo 'found a match';
                     // var_dump($this->path);
                     $this->path =  $this->routers[$i];
                     $path = $this->path;

                     // set the params to the legacy class
                     if (count($urlParams)!==0) {
                         $legacy = CORE::getInstance('legacy');
                         // echo count($url);
                         $legacy->params = $urlParams;
                     }
                     $pathM = $pathX;
                     $found = true;
                 }
             }
             // echo 'router -> '.$pathX[0].' url->'.$url[0].'|||';
                // if ($pathX[0] == $url[0]) {
                //   // echo 'found a match';
                //   // var_dump($this->path);
                //       $this->path =  $this->routers[$i];
                //       $path = $this->path;
                //
                //       // set the params to the legacy class
                //       if(isset($url[1])){
                //         $urlParams = $url;
                //         unset($urlParams[0]);
                //         // print_r($url);
                //         $legacy = CORE::getInstance('legacy');
                //         // echo count($url);
                //         $legacy->params = $urlParams;
                //       }
                //       $pathM = $pathX;
                //       $found = true;
                // }
         }


         yield $found;

         // case 3


         $urlCount = count($url);
         $pathCount = count($pathM);
         if ($urlCount == $pathCount) {
             // print_r($pathM);

             //assign parameters
             //check is it has a params property
             $xPath = explode('/:', $this->path['path']);
             if (isset($xPath[1])) {
                 $this->checkParams($url, $xPath);
             }

             yield $found;
         } else {
             yield false;
         }

         return $path;
     }




     //going to make use of a generator


     public function checkParams(array $u, array $p)
     {
         for ($i = 1; $i < count($p); $i++) {
             if (isset($u[$i])) {
                 // To be called with the params
                 $_REQUEST[$p[$i]] = $u[$i];
             } else {
                 die('The Parameter <b>'.$p[$i].'</b> is not set. <h2>Not found <i>$_GET['.$p[$i].']</i></h2>');
             }
         }
     }



     public function checkSession($url, $returnTo)
     {
         if (!CoreSession::IsLoggedIn()) {
             Core::redirect(BaseUrl.$url, $returnTo);
         }
     }
 }
