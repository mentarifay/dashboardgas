<?php

namespace App\Traits;

use App\Models\AuditLog;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        // Log create
        static::created(function ($model) {
            if (auth()->check()) {
                AuditLog::log(
                    'create',
                    $model->getTable(),
                    $model->id,
                    null,
                    $model->toArray()
                );
            }
        });

        // Log update
        static::updated(function ($model) {
            if (auth()->check()) {
                AuditLog::log(
                    'update',
                    $model->getTable(),
                    $model->id,
                    $model->getOriginal(),
                    $model->getChanges()
                );
            }
        });

        // Log delete
        static::deleted(function ($model) {
            if (auth()->check()) {
                AuditLog::log(
                    'delete',
                    $model->getTable(),
                    $model->id,
                    $model->toArray(),
                    null
                );
            }
        });
    }
}