<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contractor extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'trade',
        'contact_name',
        'contact_email',
        'compliance_status',
        'insurance_expires_on',
        'worker_count',
        'last_audit_at',
    ];

    protected function casts(): array
    {
        return [
            'insurance_expires_on' => 'date',
            'last_audit_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
