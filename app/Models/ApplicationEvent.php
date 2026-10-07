<?php

namespace App\Models;

use App\Enums\ApplicationEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'application_id',
        'user_id',
        'event_type',
        'from_value',
        'to_value',
        'meta',
        'visible_to_student',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => ApplicationEventType::class,
            'meta' => 'array',
            'visible_to_student' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
