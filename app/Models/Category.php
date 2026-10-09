<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use iLluminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['nama_kategori', 'deskripsi'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}

