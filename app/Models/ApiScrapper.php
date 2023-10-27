<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiScrapper extends Model
{
    use HasFactory;
    protected $fillable = [
        'source',
        'author',
        'title',
        'description',
        'weburl',
        'publishedAt',
    ];    
}
