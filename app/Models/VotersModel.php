<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VotersModel extends Model
{
    //

    protected $table = 'voters';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'prec_no',
        'uuid'
    ];
}
