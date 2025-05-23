<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSection extends Model
{
     protected $fillable = [
        'section_id',
        'sub_section_name',
    ];
}
