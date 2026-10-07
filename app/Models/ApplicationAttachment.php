<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAttachment extends Model
{
    protected $fillable = [
        'application_id',
        'message_id',
        'uploaded_by',
        'original_name',
        'file_path',
        'mime_type',
        'size',
    ];

    /**
     * @return BelongsTo<Application, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * @return BelongsTo<ApplicationMessage, $this>
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(ApplicationMessage::class, 'message_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
