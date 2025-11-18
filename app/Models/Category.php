<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'description'
    ];

    /**
     * Get the documents for the category
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get the folders for the category
     */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * Get documents count with folder filtering
     */
    public function getDocumentsCountAttribute()
    {
        return $this->documents()->count();
    }

    /**
     * Get folders count
     */
    public function getFoldersCountAttribute()
    {
        return $this->folders()->count();
    }
}
