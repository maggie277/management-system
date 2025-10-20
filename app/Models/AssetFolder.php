<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetFolder extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'parent_id', 'created_by'];

    // Specify the foreign key for the assets relationship
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'folder_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AssetFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(AssetFolder::class, 'parent_id');
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
