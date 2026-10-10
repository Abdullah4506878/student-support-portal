<?php

namespace App\Models;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use App\Enums\MessageType;
use App\Models\Concerns\ScopedToAdminDepartment;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * @property ApplicationStatus $status
 * @property ApplicationPriority $priority
 */
class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory, LogsActivity, ScopedToAdminDepartment;

    protected $fillable = [
        'application_no',
        'student_id',
        'department_id',
        'category_id',
        'subject',
        'body',
        'semester_at_submission',
        'priority',
        'status',
        'resolution_note',
        'rejection_reason',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => ApplicationPriority::class,
            'status' => ApplicationStatus::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['status', 'priority'])->logOnlyDirty();
    }

    /**
     * Create an application and assign its application_no from its own
     * auto-increment id, inside a transaction, so numbering never races.
     *
     * @param  array<string, mixed>  $attributes
     */
    public static function createWithApplicationNumber(array $attributes): self
    {
        return DB::transaction(function () use ($attributes) {
            $application = static::create($attributes);

            $application->update([
                'application_no' => sprintf('SC-%d-%06d', $application->created_at->year, $application->id),
            ]);

            return $application;
        });
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<ApplicationCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ApplicationCategory::class, 'category_id');
    }

    /**
     * @return HasMany<ApplicationMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ApplicationMessage::class);
    }

    /**
     * @return HasMany<ApplicationAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ApplicationAttachment::class);
    }

    /**
     * @return HasMany<ApplicationEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(ApplicationEvent::class);
    }

    /**
     * @return HasMany<InternalNote, $this>
     */
    public function internalNotes(): HasMany
    {
        return $this->hasMany(InternalNote::class);
    }

    /**
     * Student responses the admin hasn't opened this application to see
     * yet — drives the "New response" badge and dashboard attention list.
     *
     * @return HasMany<ApplicationMessage, $this>
     */
    public function unreadStudentResponses(): HasMany
    {
        return $this->messages()
            ->where('type', MessageType::StudentResponse->value)
            ->whereNull('read_at');
    }

    /**
     * Orders by priority severity (low < normal < high < urgent) using a
     * portable CASE expression, since priority is stored as a string and
     * MySQL's FIELD() has no SQLite equivalent (used in tests).
     *
     * @param  Builder<Application>  $query
     * @return Builder<Application>
     */
    public function scopeOrderByPrioritySeverity(Builder $query, string $direction = 'desc'): Builder
    {
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        return $query->orderByRaw(
            "CASE priority WHEN 'low' THEN 1 WHEN 'normal' THEN 2 WHEN 'high' THEN 3 WHEN 'urgent' THEN 4 END {$direction}",
        );
    }
}
