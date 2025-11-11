<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorBudgetTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_id', 'description', 'amount', 'type', 'transaction_date', 'created_by', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
