<?php

namespace App\Domain\Models;

class User extends Model
{

    protected $table = 'users';

    public function findByUsername(string $username)
    {
        return $this->query("SELECT * FROM users WHERE username = ?", [$username], true);
    }

    public function findById(int $id)
    {
        return $this->query("SELECT * FROM users WHERE id = ?", [$id], true);
    }

    public function getPostCount(int $userId): int
    {
        $result = $this->query("SELECT COUNT(*) as count FROM posts WHERE user_id = ?", [$userId], true);
        return $result ? (int)$result->count : 0;
    }

    public function getUserWithPostCount(int $userId)
    {
        return $this->query("
            SELECT u.*, COUNT(p.id) as post_count 
            FROM users u 
            LEFT JOIN posts p ON u.id = p.user_id 
            WHERE u.id = ? 
            GROUP BY u.id
        ", [$userId], true);
    }

}