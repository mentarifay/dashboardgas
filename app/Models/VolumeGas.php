<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolumeGas extends Model
{
    use HasFactory;

    protected $table = 'volume_gas';

    protected $fillable = [
        'data',
        'shipper',
        'tahun',
        'bulan',
        'periode',
        'bulan_date',
        'daily_average_mmscfd',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bulan_date' => 'date',
        'daily_average_mmscfd' => 'decimal:2',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePenyaluran($query)
    {
        return $query->where('data', 'PENYALURAN');
    }

    public function scopePenerimaan($query)
    {
        return $query->where('data', 'PENERIMAAN');
    }

    public function scopeByShipper($query, $shipper)
    {
        return $query->where('shipper', $shipper);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('tahun', $year);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->where('bulan', $month);
    }
}