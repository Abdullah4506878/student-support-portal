<?php

namespace App\Models;

use App\Enums\ApplicationPriority;
use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Application extends Model
{
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
}
