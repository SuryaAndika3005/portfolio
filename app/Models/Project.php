<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // Mengizinkan mass-assignment untuk seeder
    protected $guarded = [];

    // Mendefinisikan relasi ke model Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}