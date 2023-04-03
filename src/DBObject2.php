<?php

/**
 * 
 * DBObject for Database handling
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

use ReflectionProperty;

class DBObject2 extends DBObjectFunctions
{

    //ANCHOR - Stałe TDF
    // Cięcie
    const CUT_TYPE_ZERO = 1;
    const CUT_TYPE_MOVE = 2;
    const CUT_TYPE_DESTROY = 3;

    // spoiny
    const JOINT_TYP_LINIA= 11;
    const JOINT_TYP_MONTAZ = 12;
    const JOINT_TYP_HDD = 13;
    const JOINT_TYP_DP = 14;
    const JOINT_TYP_OSLONA = 15;
    const JOINT_TYP_NAPRAWA = 16;
    const JOINT_TYP_CUT_BADANIA = 17;
    const JOINT_TYP_CUT_NIEZGODNOSC = 18;
    const JOINT_TYP_CUT_TECHNOLOGICZNE = 19;


    // kody QR
    const QR_ALREADY_CONNECTED = -1;
    const QR_POSITION_TAKEN = -2;
    const QR_REMOVE_NO_PARAMS = -3;
    const QR_NOT_FOUND = -4;
    const QR_POSITION_NOT_ABCD = -5;
    const QR_NOT_IN_ELEMENT = -6;   // qr code nie należy do tego elemenu
    const QR_CIRCULLAR_CONNECTION = -7; // próbujemy podpiąć spoinę do dwóch końców elementu
    const QR_ALREADY_DISCONNECTED = -8; // próbujemy odpiąć kod, ale on nie jest podpięty
    const QR_NOT_ACTIVE = -9; // próbujemy odpiąć kod, ale on nie jest podpięty
    // obiekty
    const OBJECT_NOT_FOUND = -128;
    const OBJECT_LENGTH_TOO_SMALL = -129;   // tniemy rurę, ale za mała długość
    const OBJECT_LENGTH_SMALLER_THAN_DIAMETER = -130;   // tniemy rurę, długość < średnicy
    const OBJECT_WRONG_TYPE = -131;   // zły typ obiektu do tej operacji
    const OBJECT_WITH_ERRORS = -132;   // obiekt jest niekompletny np
 

    /**
     * [Description for getErrorDescription]
     *
     * @param mixed $value
     * 
     * @return [type]
     * 
     * Created at: 2/11/2023, 2:58:49 PM (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    //NOTE - Reflection class usage
    public static function getErrorDescription($value)
    {
        if ($value > 0) return $value;
        $map = array_flip((new \ReflectionClass('Database\DBObject2'))->getConstants());
        return (array_key_exists($value, $map) ? $map[$value] : $value);
    }


    public function print()
    {
        printf("<hr> PRINT for object: %s<br>\n", get_class($this));
        foreach ((array)$this as $key => $val) {
            if (is_object($val)) continue;
            if (is_array($val)) {
                printf("FIELD: %s, VALUE: [%s]<br>\n",  $key, json_encode($val));
            } else
                printf("FIELD: %s, VALUE: [%s]<br>\n",  $key, $val);
        }
        echo "<br>\n";
        return;
        //error_log(var_dump(get_object_vars($this)));

        return (array) $this;
    }

    public function printDIV()
    {
        return sprintf(
            "<div class='text-break text-cyan'>
        <div>Klasa: <span class='text-white'>%s</span></div>
        <div>Nazwa: <span class='text-white'>%s</span></div>
        <div>ID: <span class='text-white'>%s</span></div>
        <div>Czas stworzenia: <span class='text-white'>%s</span></div>
        <div>Aktywne w DB!: <span class='%s'>%s</span></div>
        </div>
        ",
            get_class($this),
            $this->name,
            $this->id,
            $this->time_added,
            $this->active ? "text-white" : "text-white text-sm-center text-decoration-line-through",
            $this->active ? "TAK" : "NIEAKTYWNE!"
        );
    }

    public function returnHistoryArray()
    {
        if ($this->id == -1) return null;
        $map = (new \ReflectionClass(get_class($this)))->getProperties(ReflectionProperty::IS_PUBLIC);
        //$map = (new \ReflectionClass('Database\DBObject2'))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($map as $key => $value) {
            $name = $value->getName();
            $returnArray[$name] = $this->$name;
        }
        $returnArray["class"] = get_class($this);
        $returnArray["message"] = "";
        return $returnArray;
    }

    public function returnTableArray()
    {
        if ($this->id == -1) return null;
        $map = (new \ReflectionClass(get_class($this)))->getProperties(ReflectionProperty::IS_PUBLIC);
        //$map = (new \ReflectionClass('Database\DBObject2'))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($map as $key => $value) {
            $name = $value->getName();
            $returnArray[$name] = $this->$name;
        }

        // Pola obowiązkowe, bo czemu nie :)
        $returnArray['Data stworzenia'] = date("Y-m-d",strtotime($this->time_added));
        $returnArray['Ostatnia modyfikacja'] = date("Y-m-d",strtotime($this->time_updated));
        return $returnArray;
    }

    public function getObjectList(string $sql)
    {
        $result = $this->dbHandler->getRowSql($sql);
        $className = get_class($this);
        foreach ($result as $key => $value) {
            # code...
            $resultArrayOfObjects[] = new $className($value['id']);            
        }
        return isset($resultArrayOfObjects)? $resultArrayOfObjects: null;
    }

    public function getObjectButton($color = "text-white", $title = null)
    {
        $path = explode('\\', get_class($this));
        $className =  array_pop($path);
        if(null==$title) $title = sprintf("[%s] ", $this->id, $this->name);
        return sprintf('<button class="btn %s bg-primary m-2 d-block" onclick="findElementById(\'qrcodeslibrary+%s\',%s, elementSingleV1callbackFunction )">%s</button>', $color, $className, $this->id, $title);
    }
}
