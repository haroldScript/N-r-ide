<?php

namespace App;
use \PDO;
use App\Models\Annonce;
use App\Models\User;
use App\Models\Crypto;
use App\QueryBuilder;

class DB {
  private static $pdo;
  public static function isHost() {
      return $_SERVER['SERVER_NAME'] == "localhost";
      //return true;
  }
  public static function builder(){

    return new QueryBuilder();
  }
  public static function §(){

    return self::builder();
  }
  public static function get(){
    if(!isset(self::$pdo))
      if(self::isHost())
        self::$pdo = new PDO("mysql:host=localhost;dbname=blabla",'lite','root');
      else {
       //dd('hello');
        self::$pdo = new PDO("mysql:host=localhost;dbname=u783990373_bla",'u783990373_root','actionwebpass1A');
      }
        
    return self::$pdo;
  }

  public static function getAnnonces($page=0,$limit=10) {
    $offset = ($page * $limit);
    //dd($offset);
    $query = self::get()->query("select * from annonces LIMIT $limit OFFSET $offset ", PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE , Annonce::class );
    return $query->fetchAll();
  }
  public static function getUsers($name=null){
    if(!$name)
      $query = self::get()->query("select * from users", PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class);
    else {
      // code...
      $query = self::get()->query("select * from users where email='{$name}'", PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, User::class);
    }
    return $query->fetchAll();
  }
  public static function getCountAnnonces() {
    return self::get()->query("select count(id) from annonces ")->fetch()[0];
  }

  public static function getUserVerify($user, $pass){
    if(!self::getUsers($user))
      return false;
    $id = self::getUsers($user)[0]->getID();
    
    if(isset($id)){
      $query = self::get()->query("select * from crypto where users_id={$id}", PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Crypto::class);
      //dd($query->fetchAll()[0]->getHash() . " [". $pass."]");

      if($query->fetchAll()[0]->getHash() == $pass){
        
        return true;
      }
      //dd($query->fetchAll());
      return false;
    }

    return false;
  }

  

}
