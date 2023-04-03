<?php

/**
 * 
 * DBObject2 for Database handling
 * DB Functions
 * 
 * @see       https://github.com/doomiie/gps/
 *
 *
 * @author    Jerzy Zientkowski <jerzy@zientkowski.pl>
 * @copyright 2020 - 2023 Jerzy Zientkowski
 * @license   FIXME need to have a licence
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Database;

use qrcodeslibrary\TraitDatabaseFunctions;
use qrcodeslibrary\TraitFromArray;

/**
 * Klasa do zarządzania polami.
 * KAŻDA klasa pochodna od DBObject powinna dzielić się na
 * CLASS.Fields ->  CLASS.Functions -> CLASS
 */
class DBObjectFunctions extends DBObjectFields
{
    use TraitFromArray, TraitDatabaseFunctions;
    /**
     * Konstruktor obiektu. 
     * Jeśli zainicjowany z id, ładuje z bazy danych
     * Jeśli id = null, powstaje nowy obiekt, pusty,z  id = -1
     *
     * @param integer|null $objectID
     * 
     * Created at: 1.02.2023, 10:24:32 (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    public function __construct(int $objectID = null)
    {
        $this->dbHandler = new DBHandler();
        if (!is_null($objectID)) {
            return $this->loadFromDB($objectID);
        }
        return $this->id;
    }
    /**
     * loads object from array
     *
     * @param array $arrayData
     * 
     * @return [type]
     * 
     * Created at: 1.02.2023, 11:34:07 (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    public function loadFromArray(array $arrayData)
    {
        return $this->fromArrayObject($arrayData);
    }
    /**
     * loads object from Database
     *
     * @param integer $objectID
     * 
     * @return [type]
     * 
     * Created at: 1.02.2023, 11:33:54 (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    public function loadFromDB(int $objectID)
    {
        //$sql = "select * from $this->tableName where active = 1 and id = '$objectID'";
        $sql = "select * from $this->tableName where id = '$objectID'";
        $row = $this->dbHandler->getRowSql($sql);
        if(count($row) == 0) return null;
        return $this->loadFromArray($row[0]);
    }
    /**
     * loads object from Database, based on field/value search
     * must be unique, get first result!
     *
     * @param string fieldName
     * @param string value
     * 
     * 
     * @return [object] classType object
     * 
     * Created at: 1.02.2023, 11:33:54 (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    public function loadFromDBFieldValue(string $fieldName, string $value)
    {
        $sql = "select * from $this->tableName where  $fieldName = '$value'";
        //$sql = "select * from $this->tableName where active = 1 and $fieldName = '$value'";
        $row = $this->dbHandler->getRowSql($sql);
        if(count($row) == 0) return null;
        return $this->loadFromArray($row[0]);
    }
    /**
     * saves NEW object into the database
     *
     * @return [type]
     * 
     * Created at: 1.02.2023, 14:23:48 (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    public function saveToDB()
    {
        $fields = get_object_vars($this);
        unset($fields['id']);
        unset($fields['tableName']);
        unset($fields['time_added']);
        unset($fields['time_updated']);
        unset($fields['dbHandler']);
        $columns = "`" . implode('`,`', array_keys($fields)) . "`";
        $placeholders = implode(',', array_map(function ($field) {
            return  sprintf("'%s'", $this->$field);
        }, array_keys($fields)));
        $sql = "INSERT INTO `$this->tableName` ($columns) VALUES ($placeholders);";
        //error_log($sql);
        
        $result = $this->dbHandler->insertSql($sql);
        $this->id = $result;
        DBLog::LogMe($this, "SAVE:", $sql);
        \Database\DBLog::LogMe($this, __FUNCTION__ . " " . $this->id, basename(__FILE__) . "[".__LINE__."]" );
        return $result;
    }
    /**
     * updates EXISTING object into the database
     *
     * @return [type]
     * 
     * Created at: 1.02.2023, 14:24:03 (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    public function updateToDB()
    {
        if (-1 == $this->id) return -1;
        $fields = get_object_vars($this);
        unset($fields['id']);
        unset($fields['tableName']);
        unset($fields['dbHandler']);
        unset($fields['time_added']);
        unset($fields['time_updated']);
        $this->time_updated = "";
        $placeholders = implode(',', array_map(function ($field) {
            return  sprintf("`%s` = '%s'", $field, $this->$field);
        }, array_keys($fields)));
        $sql = "UPDATE `$this->tableName` SET $placeholders WHERE `id` = '$this->id';";
        $result = $this->dbHandler->updateSql($sql);
        
        \Database\DBLog::LogMe($this, "UPDATE:", $sql);
        //\Database\DBLog::LogMe($this, __FUNCTION__ . " " . $this->id, basename(__FILE__) . "[".__LINE__."]" );
        return $result;
    }

    public function Activate()
    {
        $this->active = 1;
        \Database\DBLog::LogMe($this, __FUNCTION__ . " " . $this->id, basename(__FILE__) . "[".__LINE__."]" );
        
        return $this->updateToDB();
    }

    public function Deactivate()
    {
        $this->active = 0;
        \Database\DBLog::LogMe($this, __FUNCTION__ . " " . $this->id, basename(__FILE__) . "[".__LINE__."]" );
        return $this->updateToDB();
    }

    public function Delete()
    {
        $sql = "DELETE FROM `$this->tableName` WHERE `id` = '$this->id';";
        \Database\DBLog::LogMe($this, __FUNCTION__ . " " . $this->id, basename(__FILE__) . "[".__LINE__."]" );
        return $this->dbHandler->updateSql($sql);
    }

    public function findTableFieldValue(string $tableName, string $fieldName, $value)
    {
        $sql = "SELECT * FROM `$tableName` WHERE active = 1 and `$fieldName` = '$value';";
        return $this->dbHandler->getRowSQL($sql);
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getLinkToSingleElement($title = null)
    {
        return sprintf("<a href='%s-single.php?index=%s' target='_blank'>%s</a>",$this->getTableName(), $this->id, is_null($title)?$this->name:$title);
    }
}
