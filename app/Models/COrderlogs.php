<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class COrderlog extends Model
{
    protected $table = 'order_status_logs'; // Explicitly define the table name

    public $timestamps = false; // Since you're using 'changed_at' instead of default timestamps

    protected $fillable = [
        'order_id',
        'status',
        'changed_by',
        'changed_at',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(COrders::class, 'order_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
