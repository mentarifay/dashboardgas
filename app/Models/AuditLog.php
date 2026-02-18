<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to log activity
    public static function log($action, $tableName = null, $recordId = null, $oldValue = null, $newValue = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}