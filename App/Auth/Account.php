<?php

namespace App\Auth;

use DB, Config, User;

class Account extends DB{  
    /**
     * Login User
     * @param  string  $username
     * @param  string  $password
     * @param  boolean [$remember = false]
     * @return boolean
     */
    public static function login($username, $password, $remember = false){

        $user = DB::select('users', ['*'], ['username' => $username]);

        $msg = 'These credentials does not match any record in our database';

        if($user->rowCount() == 0) return $msg;
        
        $user = $user->fetch();
    
        if(!password_verify($password, $user['password'])) return $msg;

        if($remember) {
            $cookie = sha1(uniqid(microtime()));
            DB::updateWhere('users', ['cookie' => $cookie], ['id' => $user['id']]);
            self::setCookie('remeberme', $user['cookie']);
        } else {
            self::removeCookie('remeberme');
        }

        $_SESSION['uuid'] = $user['id'];
        $_SESSION['rank'] = isset($user['rank']) ? $user['rank'] : null;

        return true;
    }


    /**
     * Register a user
     * @param  string  $username
     * @param  string  $pw1
     * @param  string  $pw2
     * @param  string  $mail
     * @return boolean
     */
    public static function register($username, $pw1, $pw2, $mail){
        if($pw1 != $pw2) return 'passwords does not match';

        if(DB::select('users', ['username'], ['username' => $username])->rowCount() > 0) return 'Username already taken';

        return DB::insert('users',[[
            'username'  => $username,
            'password'  => password_hash($pw1, PASSWORD_DEFAULT),
            'mail'      => $mail,
        ]]);

    }

    /**
     * Logout a user
     * @author Agne *degaard
     */
    public static function logout(){
        self::removeCookie('remeberme');
        session_destroy();
    }

    /**
     * Set a $_COOKIE param
     * @param string $name
     * @param string $value
     */
    public static function setCookie($name, $value){
        setcookie($name, $value, time()+Config::$cookie_time);
    }

    /**
     * Remove a $_COOKIE param
     * @param string $name
     */
    public static function removeCookie($name){
        unset($_COOKIE[$name]);
        setcookie($name, null, -1);
    }

    /**
     * Return if the user is logged in
     * @return boolean
     */
    public static function isLoggedIn(){
        return isset($_SESSION['uuid']);
    }

    /**
     * Change a users password
     * @author Agne *degaard
     * @param  object User    $user
     * @param  string $pw
     * @param  string $newPw
     * @param  string $newpw2
     * @return string string
     */
    public static function changePassword(User $user, $pw, $newPw, $newpw2){
        if($newPw !== $newpw2) return 'The new passwords does not match';

        if(!password_verify($pw, $user->password)) return 'Old password is wrong';

        $msg = DB::update(['password' => password_hash($newPw, PASSWORD_DEFAULT)], 'users', ['id' => $user->id]);

        return $msg;
    }

    // @TODO
    public function changeEmail($newMail){
      return 0;
    }

    public static function get_id(){
        return (isset($_SESSION['uuid']) ? $_SESSION['uuid'] : false);
    }
}