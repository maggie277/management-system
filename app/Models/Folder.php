<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'parent_id', 'created_by'];

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

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
}
