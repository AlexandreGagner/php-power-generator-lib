<?php
/**
 * Gestion des administrateur et permissions
 */
class Admin
{
    function __construct($id)
    {
        $admin = DB::queryFirstRow("SELECT * FROM users WHERE id = %i", $id);
        foreach ($admin as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function Login($login, $password)
    {
        global $auth;

        try {
            $auth->loginWithUsername($login, $password, $rememberDuration);
            return true;
        }
        catch (\Delight\Auth\UnknownUsernameException $e) {
            return false;
        }
        catch (\Delight\Auth\AmbiguousUsernameException $e) {
            return false;
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            return false;
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            return false;
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            return false;
        }
    }

    public static function Logout()
    {
        global $auth;
        $auth->logout();
    }
    
}