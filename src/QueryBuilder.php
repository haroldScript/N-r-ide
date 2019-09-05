<?php

namespace App;

class QueryBuilder {

    private $query;

    public function __construct(){
        $this->query = '';
    }
    public function select($args='*'){
        $this->query = 'select ' . $args;
        return $this;
    }

    public function from($args='*'){
        $this->query .= ' from '.$args;
        return $this;
    }

    public function where($cble1, $cble2){
        $this->query .= " where $cble1={$cble2}";
        return $this;
    }

    public function insertInto($cble, $array){
        $this->query .= " INSERT INTO $cble ( " . implode(" ,",$array)." ) VALUES ( :".implode(",:",$array)." )";
        return $this;
    }
    public function resetForKey($kCheck="0", $table="*"){
        $this->query .= "SET FOREIGN_KEY_CHECKS={$kCheck}; TRUNCATE TABLE {$table} ;";

        return $this;
    }
    public function restoreForKey($kCheck="1"){
        $this->query .= " SET FOREIGN_KEY_CHECKS=$kCheck;";

        return $this;
    }
    public function get(){
        $inst = $this->query;
        $this->query = '';
        return $inst;
    }
}