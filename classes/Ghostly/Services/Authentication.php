<?php

namespace Ghostly\Services;

class Authentication {
    public function verifyUserAndPassword($username, $password) {
        $user = \ORM::forTable('users')->where('username', $username)->findOne();
        if ($user === FALSE) {
            return FALSE;
        }

        return password_verify($password, $user->password);
    }

    public function getUserIdFromUser($username) {
        $user = \ORM::forTable('users')->where('username', $username)->findOne();
        if ($user === FALSE) {
            return FALSE;
        }
        
        return $user->id();
    }
}