<?php

/**
 * 
 * DBObject2 for Database handling
 * DBObiectFields is 
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

/**
 * Klasa do zarządzania polami.
 * KAŻDA klasa pochodna od DBObject powinna dzielić się na
 * CLASS.Fields ->  CLASS.Functions -> CLASS
 */
class DBObjectFields
{
    /**
     * Nazwa tabeli, z której będą pobierane dane
     *
     * @var string
     */
    protected $tableName = "";
    /**
     * Próbuję zainicjować z -1 w tym miejscu, żeby dla insert nie kasować ID, tylko zwracać -1
     *
     * @var [type]
     */
    public $id = -1;
    /**
     * Domyślne pola dla wszystkich obiektów
     *     
     */
    /**
     * Nazwa obiektu, generowana podczas tworzenia obiektu!
     *
     * @var [type]
     */
    public $name;
    /**
     * Czy obiekt jest aktywny w bazie danych
     *
     * @var [type]
     */
    public $active = 1;
    /**
     * Czas dodania do bazy danych, niezapisywalny
     *
     * @var [type]
     */
    public $time_added;
    /**
     * Timestamp, when the row was updated.
     * ALTER TABLE `*`  ADD `time_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  ON UPDATE CURRENT_TIMESTAMP AFTER `refID`;
     *
     * @var [type]
     */
    public $time_updated;


    /**
     * Uchwyt do bazy danych
     *
     * @var [type]
     */
    protected $dbHandler;

  

  
}
