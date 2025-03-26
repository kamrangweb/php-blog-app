<?php

namespace App\Domain\Models;


class Tag extends Model
{

    protected $table = 'tags';

    public function posts(): array
    {
        return $this->query(
            "SELECT p.* FROM tags p
                JOIN post_tag pt ON pt.post_id = p.id
                WHERE pt.tag_id = ? ORDER BY p.created_at DESC",
            [$this->id]);
    }


}