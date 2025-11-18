<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'created_by',
        'category_id'
    ];

    /**
     * The user who created the folder
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The category this folder belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The parent folder (nullable)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Child folders (subfolders)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Documents inside this folder
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Scope for root folders (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the folder path as a string
     */
    public function getPathAttribute(): string
    {
        $path = [];
        $folder = $this;

        while ($folder) {
            $path[] = $folder->name;
            $folder = $folder->parent;
        }

        return implode(' / ', array_reverse($path));
    }

    /**
     * Check if folder has any content (documents or subfolders)
     */
    public function getHasContentAttribute(): bool
    {
        return $this->documents()->count() > 0 || $this->children()->count() > 0;
    }
}
