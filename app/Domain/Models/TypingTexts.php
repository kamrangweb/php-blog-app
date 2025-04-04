<?php

namespace App\Domain\Models;


class TypingTexts extends Model
{
    
    protected $table = 'typing_texts';    

    public function getTypingTexts()
    {
        $query = "SELECT text FROM typing_texts";
        return $this->query($query);
    }


}