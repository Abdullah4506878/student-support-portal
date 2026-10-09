<?php

namespace App\Concerns;

use App\Models\ApplicationCategory;
use App\Models\Setting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait ApplicationValidationRules
{
    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function categoryIdRules(int $departmentId): array
    {
        return [
            'required',
            Rule::exists(ApplicationCategory::class, 'id')
                ->where('department_id', $departmentId)
                ->where('is_active', true),
        ];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function subjectRules(): array
    {
        return ['required', 'string', 'max:'.$this->subjectMaxLength()];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function bodyRules(): array
    {
        return ['required', 'string', 'max:'.$this->bodyMaxLength()];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function attachmentsRules(): array
    {
        return ['nullable', 'array', 'max:3'];
    }

    /**
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function attachmentRules(): array
    {
        return ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'];
    }

    protected function subjectMaxLength(): int
    {
        return (int) Setting::get('applications.subject_max_length', '100');
    }

    protected function bodyMaxLength(): int
    {
        return (int) Setting::get('applications.body_max_length', '500');
    }

    /**
     * Maps each "attachments.N" validation key to that file's own original
     * name, so mimes/max messages can name the offending file instead of
     * a generic ":attribute".
     *
     * @param  array<int, TemporaryUploadedFile>  $files
     * @return array<string, string>
     */
    protected function attachmentAttributeNames(array $files): array
    {
        $names = [];

        foreach ($files as $index => $file) {
            $names["attachments.{$index}"] = $file->getClientOriginalName();
        }

        return $names;
    }

    /**
     * @return array<string, string>
     */
    protected function applicationMessages(): array
    {
        return [
            'category_id.required' => __('Select a category.'),
            'category_id.exists' => __('Select a valid category.'),
            'subject.required' => __('Enter a subject.'),
            'body.required' => __('Describe your issue.'),
            'attachments.max' => __('You can attach up to 3 files.'),
            'attachments.*.mimes' => __(':attribute: only PDF, JPG, PNG, DOC or DOCX files are allowed.'),
            'attachments.*.max' => __(':attribute is larger than 5 MB. Please upload a smaller file.'),
        ];
    }
}
