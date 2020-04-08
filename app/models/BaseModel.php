<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model{
    private static $_instance=array();
    protected $connection;
    protected $table;
    public $timestamps=false;

    public static function factory($table,$connection='mysql'){
        if(!isset(self::$_instance[$connection.'.'.$table]) || !is_object(self::$_instance[$connection.'.'.$table])){
            self::$_instance[$connection.'.'.$table] = new BaseModel;
        }
        self::$_instance[$connection.'.'.$table]->table=$table;
        self::$_instance[$connection.'.'.$table]->connection=$connection;
        return self::$_instance[$connection.'.'.$table];
    }

}