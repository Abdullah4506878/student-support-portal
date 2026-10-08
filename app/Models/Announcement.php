<?php

namespace App\Models;

use App\Models\Concerns\ScopedToAdminDepartment;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $department_id
 * @property bool $is_active
 * @property Carbon|null $publish_at
 * @property Carbon|null $expires_at
 */
class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory, ScopedToAdminDepartment;

    protected $fillable = [
        'department_id',
        'created_by',
        'title',
        'body',
        'image_path',
        'attachment_path',
        'publish_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
