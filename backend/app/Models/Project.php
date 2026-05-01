<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'code',
        'name',
        'client_name',
        'city',
        'status',
        'permit_status',
        'progress_percent',
        'budget_total',
        'budget_spent',
        'start_date',
        'target_date',
        'manager_id',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'target_date' => 'date',
            'budget_total' => 'decimal:2',
            'budget_spent' => 'decimal:2',
        ];
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function permits(): HasMany
    {
        return $this->hasMany(Permit::class);
    }

    public function contractors(): HasMany
    {
        return $this->hasMany(Contractor::class);
    }

    public function diaryEntries(): HasMany
    {
        return $this->hasMany(SiteDiaryEntry::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }
}
