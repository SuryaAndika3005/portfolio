<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Mendefinisikan relasi ke model Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}