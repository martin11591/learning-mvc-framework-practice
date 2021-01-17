<?php

namespace app\core;

/**
 * Class Session
 * 
 * @author  Marcin Podraza
 * @package app\core
 */

class Session
{
    private $guid;
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION['flash_guid'])) {
            $this->guid = 'flash_' . Utils::randomBytes();
            $_SESSION['flash_guid'] = $this->guid;
        } else {
            $this->guid = $_SESSION['flash_guid'];
        }
        $flashMessages = isset($_SESSION[$this->guid]) ? $_SESSION[$this->guid] : [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['toRemove'] = true;
        }
        $_SESSION[$this->guid] = $flashMessages;
    }

    public function setFlash($key, $value)
    {
        $_SESSION[$this->guid][$key] = [
            'toRemove' => false,
            'value' => $value
        ];
    }

    public function getFlash($key)
    {
        return isset($_SESSION[$this->guid][$key]['value']) ? $_SESSION[$this->guid][$key]['value'] : false;
    }

    public function hasFlash()
    {
        if (isset($_SESSION[$this->guid])) {
            return count($_SESSION[$this->guid]) > 0;
        }
        return false;
    }

    public function getAllFlash()
    {
        $flashMessages = [];
        foreach ($_SESSION[$this->guid] as $key => $value) {
            $flashMessages[$key] = $this->getFlash($key);
        }
        return $flashMessages;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $flashMessages = isset($_SESSION[$this->guid]) ? $_SESSION[$this->guid] : [];
        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['toRemove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[$this->guid] = $flashMessages;
    }
}