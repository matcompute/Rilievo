<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteDiaryEntry extends Model
{
    protected $fillable = [
        'project_id',
        'author_id',
        'entry_date',
        'weather',
        'workforce_count',
        'completed_work',
        'blockers',
        'safety_note',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
