<?php

/**
 * DBConfig 
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

class DBConfig
{
    public static $dbArray = null;

    public static function load($fileName)
    {
        if(!file_exists($fileName))
        {
            throw new Exception("File does not exists: " . $fileName);
            return null;
        }
        $string = file_get_contents($fileName);
        if ($string === false) {
            return null;
        }
        
        self::$dbArray = json_decode($string, true);
        if (self::$dbArray === null) {
            return null;
        }
        return 1;

    }
}
