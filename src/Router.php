<?php
namespace App;
use AltoRouter;

class Router {

  private $patchView;
  private $router;
  private $content;
  private $map;
  private $ctrl;
  private $mode;
  private $acl;

  public function __construct($view, $mode=false,$title="",$acl="acceuil.php"){
    $this->patchView = $view;
    $this->router = new AltoRouter();
    $this->map = [];
    $this->ctrl = [];
    $this->title = $title;
    $this->mode = $mode;
    $this->acl = $acl;
  }

  public function get($url,  $file , $name, $ctrl=null){
    $this->router->map("GET",$url,$file,$name);
    $this->map[$name][] = "v=".explode("/",$file)[0] ;
    $this->map[$name][] = "f=".explode("/",$file)[1] ;
    if(isset($ctrl)){
      $this->ctrl[$name] = $ctrl;
    }
    return $this;
  }
  public function post($url,  $file , $name, $ctrl=null){
    $this->router->map("POST",$url,$file,$name);
    
    $this->map[$name][] = "v=".explode("/",$file)[0] ;
    $this->map[$name][] = "f=".explode("/",$file)[1] ;

    if(isset($ctrl)){
      $this->ctrl[$name] = $ctrl;
    }
    return $this;
  }
  public function run(){
    $user = isset($_SESSION['user']) ? unserialize($_SESSION['user']) : false;

    $this->content = null;
    $match = $this->router->match();

    $view = $match['target'];
    $router = $this->router;
    

    //execution des controllers
    
    if($this->mode && isset($_GET['n']) && isset($this->ctrl[$_GET['n']])){
      //dd($match[$_GET['n']]);
      $this->ctrl[$_GET['n']]();
    }
        
    $defaultLayout = str_replace("f=","",str_replace("v=","",implode("/",array_values($this->map)[0])));
    $dl = explode("/",$defaultLayout);

    ob_start();
    if($this->mode && is_string($view) && $view != $defaultLayout/*"default/layout"*/ || (isset($_GET['v']) && isset($_GET['f'])) ) {
      if($_GET['v'] == $dl[0] && $_GET['f'] == $dl[1])
        require $this->patchView . DIRECTORY_SEPARATOR . $dl[0] . DIRECTORY_SEPARATOR . $this->acl;
      else{
        require $this->patchView . DIRECTORY_SEPARATOR .((string) !(isset($_GET['v']) && isset($_GET['f'])) ? 
        $view .'.php' :
        $_GET['v'] . DIRECTORY_SEPARATOR . $_GET['f']) .'.php';
      }
      
    }
    if(!$this->mode){
      require $this->patchView . DIRECTORY_SEPARATOR . $view .".php";

    } 
    $this->content = ob_get_clean();
    
    require $this->patchView . DIRECTORY_SEPARATOR . $defaultLayout.".php";
    return $this;
  }
  public function url($namedRoute, $getArray=null){
    if(isset($getArray))
    {
      if($this->mode)
        return $this->router->generate(array_keys($this->map)[0]). '?' . implode('&', $this->map[$namedRoute]) .'&'. implode('&',$getArray) . '&n='.$namedRoute;
      else
        return $this->router->generate($namedRoute) . '?' . implode('&',$getArray);

    }
    if($this->mode){
      
        return $this->router->generate(array_keys($this->map)[0]).  '?' . implode('&', $this->map[$namedRoute]) .'&n='.$namedRoute;
    }
    return $this->router->generate($namedRoute);
  }
  public function routeEq($name){
    if($this->mode && $name)
      if(isset($_GET['n']))
        if($_GET['n'] == $name)
          return true;
        else
          return false;
      else 
        return false;
    elseif ($name){
      if($this->router->match()['name'] == $name)
        return true;
      else 
        return false;
    }
    else
      return false;
  }
  
}

 ?>
