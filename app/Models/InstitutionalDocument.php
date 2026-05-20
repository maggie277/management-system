<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionalDocument extends Model
{
    use HasFactory;

    protected $table = 'institutional_documents';

    protected $fillable = [
        'name',
        'description',
        'category',
        'file_path',
        'file_extension',
        'file_size',
        'uploaded_by',
        'download_count',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'download_count' => 'integer',
        'file_size' => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedSize()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
