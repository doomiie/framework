<?php

/**
 * 
 * DBUser for user handling
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

class userFields extends DBObject2
{
    public $tableName = "user";
    public $username;
    public $email;
    public $password;
    public $role;       // unknown implementation yet
    public $projectID;  // id projektu, do którego ma dostęp użytkownik

}

class userFunctions extends userFields
{

    public function authenticate($password)
    {
        //echo $password . " <" . $this->password;
        if (password_verify($password, $this->password)) {
            $_SESSION['qrcode_user_id'] = $this->id;
            return true;
        }
        return false;
    }

    public function hasRole(array $roles = null)
    {
        if (null == $roles) return true;
        //error_log(json_encode(explode(",",$this->role)));
        foreach (explode(",",$this->role) as $key => $value) {
            error_log(json_encode($value));
            error_log(json_encode($roles));
            if(in_array($value, $roles))
            return true;
        }
        return false;
        return in_array(explode(",",$this->role), $roles);
    }

    /**
     * Funkcja na każdej strone, ogranicza dostęp do
     * - zalogowanego usera
     * - jeśli są priviledges, a user ich nie spełnia, to odsyła na odpowiednią stronę
     * - jeśli nie ma usera, odsyła do login
     * 
     *
     * @param array|null $priviledgesArray
     * 
     * @return ObjectUser user
     * 
     * Created at: 3/1/2023, 7:33:39 PM (Europe/Warsaw)
     * @author     Jerzy "Doom_" Zientkowski 
     * @see       {@link https://github.com/doomiie} 
     */
    public static function goHome(array $priviledgesArray = null)
    {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        if (!isset($_SESSION['qrcode_user_id'])) {

            header("Location: login.php");
            exit;
        }
        $user = new ObjectUser($_SESSION['qrcode_user_id']);
        //error_log(json_encode($priviledgesArray));
        if (!$user->hasRole($priviledgesArray)) {
            //TODO - zrobić stronę z 'nie masz przywilejów'
            
            header("Location: index.php");
            exit;
        }
        return $user;
    }
}

class ObjectUser extends userFunctions
{
}
