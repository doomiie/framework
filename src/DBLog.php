<?php

/**
 * 
 * DBLog for Database  log handling
 * 
 * @see       https://github.com/doomiie/gps/

 *
 *
 * @author    Jerzy Zientkowski <jerzy@zientkowski.pl>
 * @copyright 2020 - 2022 Jerzy Zientkowski
 

 * @license   FIXME need to have a licence
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 * No usrało mi się
 */

namespace Database;

use qrcodeslibrary\ObjectElement;

class DBLog extends DBObject2
{
    protected $tableName = "log";
    public $message;
    public $_sql;
    public $refType;
    public $refId = -1;
    public $elementId = -1;

    public static function UserLog($object, $message, $_sql = "", $userName = "", $forceRef=null)
    {
        $userName = $object->getLoggedUserID();
        self::LogMe($object, $message, $_sql, $userName, $forceRef);
    }


    public static function LogMe($object, $message, $_sql = "", $userName = "SYSTEM", $forceRef = null)
    {
        if(get_class($object) == "Database\\DBLog") return -1;
        if(strlen($_sql) > 500)
        {
            $rest = substr($_sql, 0, 500);
            DBLog::LogMe($object, $message, $rest);
            $_sql= substr($_sql, 500);
        }
        
        $log = new DBLog();
        $log->elementId = $object->id;
        $log->message = $message;
        $log->refType = $object->getTableName();
        $log->name = $userName;
        
        $better = mysqli_real_escape_string($log->dbHandler->getHandle(), $_sql);
        $log->_sql = $better;
        //$log->refId = $object->id;

        // jeśli obiekt nie ma pola element id
        $log->refId = $object->id;
        // jeśli obiekt ma pole element id, dodaj ten obiekt też
        if(property_exists($object, "elementId"))
        {
            $ref = new ObjectElement($object->elementId);
            // za dużo logów tutaj
           // self::LogMe($ref, "REF:".$message, $_sql);    
            $log->refId = $ref->id;
        }
        if(null != $forceRef)
        $log->refId = $forceRef;

        
        return $log->saveToDB();
    }

    public function Log(string $message)
    {

        if(strlen($message) > 500)
        {
            $rest = substr($message, 0, 500);
            $this->Log($rest);
            $message = substr($message, 500);
        }
        //error_log("DBLOG:" . $message);
        $this->message = $message;
        $this->elementId = -1;
        $this->saveToDB();
    }

 
    public function returnHistoryArray()
    {
        $returnArray = parent::returnHistoryArray();
        
        $returnArray["message"] = $this->message . "[".$this->refType . " " . $this->elementId."]";
        return $returnArray;

    }
}
