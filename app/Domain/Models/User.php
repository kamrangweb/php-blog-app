<?php

namespace App\Domain\Models;

class User extends Model
{

    protected $table = 'users';

    public function findByUsername(string $username)
    {
        return $this->query("SELECT * FROM users WHERE username = ?", [$username], true);
    }


}