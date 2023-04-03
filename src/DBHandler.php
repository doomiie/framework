<?php

/**
 * DBHAndler for USerManagement
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
 */

namespace Database;

use Exception;

class DBHandler
{
    protected $handle;
    protected static $dbHandle = false;

    public function getHandle()
    {
        return $this->handle;
    }

    public function init()
    {
        //error_log("No such file as database.json! in " . __DIR__);
        if(DBHandler::$dbHandle === false)
        {
            if(null === DBConfig::load(__DIR__."/db.json"))
            {
                throw new Exception("No such file as database.json!");
                return null;
            }
            //DBHandler::$dbHandle = mysqli_connect(DBConfig::$dbArray['host'],DBConfig::$dbArray['user'],DBConfig::$dbArray['password'],DBConfig::$dbArray['database']);
            DBHandler::$dbHandle = mysqli_connect(DBConfig::$dbArray['host'],DBConfig::$dbArray['username'],DBConfig::$dbArray['password'],DBConfig::$dbArray['database']);
            mysqli_set_charset(DBHandler::$dbHandle, "utf8mb4");                
        }
        $this->handle = DBHandler::$dbHandle;
        return DBHandler::$dbHandle;        

        if ($this->handle === false) {
			throw new Exception(mysqli_connect_error());
			return null;
		}
    mysqli_set_charset($this->handle, "utf8mb4");    
    return $this->handle;

    }

    public function __construct()
    {
        try {
            $this->init();
        } catch (\Throwable $th) {
            throw $th;
        };	 
    return $this->handle;
    }

    protected function dbExec($sql)
    {
            //error_log("Executing : " . $sql);
            return mysqli_query($this->handle, $sql);
    }

       
    public  function getRowSql(string $sql)
    {
        $result = $this->dbExec($sql);
        //error_log(sprintf("getRowSQL: SQL [%s]<br>\n", $sql));
        if($result instanceof \mysqli_result)
        {
        return mysqli_fetch_all($result,MYSQLI_ASSOC);
        }
        else
        {
            return null;
        }
    }

    /**
     * RUNS insert sql
     *
     * @param string $sql
     * 
     * @return int inserted row ID or error description
     * 
     */
    public  function insertSql(string $sql)
    {
        $result = $this->dbExec($sql);
        if($result)
        {
        return mysqli_insert_id($this->handle);
        }
        else
        {
            error_log(sprintf("SQL insert error: %s\n", mysqli_error($this->handle)));
            return mysqli_error($this->handle);
        }
    }

    public  function updateSql(string $sql)
    {
        $result = $this->dbExec($sql);
        //error_log(sprintf("result: [%s] for SQL [%s]<br>\n", $result,$sql));
        if($result)
        {
        return mysqli_affected_rows($this->handle);
        }
        else
        {
            return mysqli_error($this->handle);
        }
    }
}
