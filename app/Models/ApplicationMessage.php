<?php

namespace App\Models;

use App\Enums\MessageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationMessage extends Model
{
    protected $fillable = [
        'application_id',
        'sender_id',
        'parent_id',
        'type',
        'body',
        'read_at',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => MessageType::class,
            'read_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * A request (info_request / document_request) is open while this is null.
     */
    public function isOpen(): bool
    {
        return $this->responded_at === null;
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
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * @return BelongsTo<ApplicationMessage, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ApplicationMessage::class, 'parent_id');
    }

    /**
     * @return HasMany<ApplicationMessage, $this>
     */
    public function responses(): HasMany
    {
        return $this->hasMany(ApplicationMessage::class, 'parent_id');
    }

    /**
     * @return HasMany<ApplicationAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ApplicationAttachment::class, 'message_id');
    }
}
