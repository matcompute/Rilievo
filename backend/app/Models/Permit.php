<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permit extends Model
{
    protected $fillable = [
        'project_id',
        'owner_id',
        'permit_name',
        'authority',
        'reference_code',
        'status',
        'due_date',
        'submitted_at',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'submitted_at' => 'date',
            'approved_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
