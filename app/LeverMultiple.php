<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeverMultiple extends Model
{
    protected $table = 'lever_multiple';
    public $timestamps = false;

    public function getQuotesAttribute()
    {
        return unserialize($this->attributes['quotes']);
    }
}
