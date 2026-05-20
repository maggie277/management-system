<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'description',
        'department',
        'document_type',
        'effective_date',
        'expiry_date',
        'reference_number',
        'status',
        'version',
        'created_by',
        'folder_id',
        'category_id',
        'is_public',
        'is_active',
        // HR specific fields
        'employee_id',
        'contract_type',
        'position',
        'salary_scale',
        // Donor contract fields
        'donor_name',
        'contract_value',
        'currency',
        'project_name',
        'reporting_requirements',
        // Budget fields
        'fiscal_year',
        'budget_type',
        'total_amount',
    ];

    protected $casts = [
        'effective_date'  => 'date',
        'expiry_date'     => 'date',
        'contract_value'  => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'is_public'       => 'boolean',
        'is_active'       => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // ── Status accessor ───────────────────────────────────────────────────────
    //
    // IMPORTANT: accept $value so the stored DB value is respected.
    // Only fall back to expiry-date logic when nothing is stored.
    //
    public function getStatusAttribute($value)
    {
        // If a status is explicitly stored (draft, pending, approved, active, etc.)
        // return it as-is so budget/donor documents keep their real status.
        if (!empty($value)) {
            return $value;
        }

        // Fallback: derive status from expiry_date for legacy rows without a stored status.
        if (!$this->expiry_date) {
            return 'active';
        }

        if (Carbon::parse($this->expiry_date)->isPast()) {
            return 'expired';
        }

        if (Carbon::parse($this->expiry_date)->diffInDays(now()) <= 30) {
            return 'expiring_soon';
        }

        return 'active';
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getFileSizeFormattedAttribute()
    {
        $size = (int) $this->file_size;

        if ($size >= 1073741824) {
            return number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            return number_format($size / 1024, 2) . ' KB';
        }

        return $size . ' bytes';
    }

    public function getFileExtensionAttribute()
    {
        if ($this->file_path) {
            return strtoupper(pathinfo($this->file_path, PATHINFO_EXTENSION));
        }

        // Budget / reference documents have no physical file; derive from document_type
        return match ($this->document_type) {
            'budget'          => 'BUDGET',
            'donor_contract'  => 'CONTRACT',
            'staff_contract'  => 'CONTRACT',
            default           => 'REF',
        };
    }

    public function getHasFileAttribute(): bool
    {
        return !empty($this->file_path);
    }

    public function getContractTypeFormattedAttribute()
    {
        return match ($this->contract_type) {
            'permanent'   => 'Permanent',
            'fixed_term'  => 'Fixed Term',
            'consultancy' => 'Consultancy',
            'internship'  => 'Internship',
            default       => 'Unknown',
        };
    }

    public function getBudgetTypeFormattedAttribute()
    {
        return match ($this->budget_type) {
            'operational' => 'Operational Budget',
            'program'     => 'Program Budget',
            'capital'     => 'Capital Budget',
            'proposal'    => 'Proposal Budget',
            default       => 'Other',
        };
    }

    public function getDepartmentFormattedAttribute()
    {
        return match ($this->department) {
            'hr'         => 'Human Resources',
            'finance'    => 'Finance',
            'programs'   => 'Programs',
            'procurement'=> 'Procurement',
            'admin'      => 'Administration',
            'general'    => 'General',
            default      => 'Unknown',
        };
    }

    public function getDocumentTypeFormattedAttribute()
    {
        return match ($this->document_type) {
            'staff_contract'  => 'Staff Contract',
            'donor_contract'  => 'Donor Contract',
            'budget'          => 'Budget',
            'policy'          => 'Policy',
            'report'          => 'Report',
            'proposal'        => 'Proposal',
            'agreement'       => 'Agreement',
            'other'           => 'Other',
            default           => 'Unknown',
        };
    }

    public function getContractValueFormattedAttribute()
    {
        if ($this->contract_value && $this->currency) {
            return $this->currency . ' ' . number_format($this->contract_value, 2);
        }

        return 'N/A';
    }

    public function getTotalAmountFormattedAttribute()
    {
        if ($this->total_amount && $this->currency) {
            return $this->currency . ' ' . number_format($this->total_amount, 2);
        }

        return 'N/A';
    }

    public function getCategoryNameAttribute()
    {
        return $this->documentCategory ? $this->documentCategory->name : 'Uncategorized';
    }
}
