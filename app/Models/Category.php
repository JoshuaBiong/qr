<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['barangay', 'purok'];

    public function voters()
    {
        return $this->hasMany(VotersModel::class);
    }
}

