<?php

namespace App\Services;

use App\Models\Template;
use App\Models\TemplateField;
use App\Models\TemplateRecordValue;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * @internal Transforms template record values into a structured format.
 * @final
 */
final class TemplateRecordTransformer
{
    /**
     * Prevent instantiation
     */
    private function __construct() {}

    /**
     * Transform the record values into an array of cohesive records.
     *
     * @param Collection $values
     * @param Template $template
     * @return array
     */
    public static function transformRecords(Collection $values, Template $template): array
    {
        return $values
            ->groupBy('template_record_id')
            ->map(fn($values) => self::transformRecord($values, $template))
            ->values()
            ->all();
    }

    /**
     * Transform the record values for one record into a cohesive record.
     *
     * @param Collection $values
     * @param Template $template
     * @return array
     */
    private static function transformRecord(Collection $values, Template $template): array
    {
        // Set up the record with the ID of the template record
        $record = [
            'id' => $values->first()->template_record_id,
        ];

        // Go over each of the record values, and try to map them to the field name
        foreach ($values as $value) {
            $field = self::getField($template, $value->template_field_id);

            if ($field) {
                $record[$field->name] = $value->getTypedValue($field->type);
            }
        }

        return $record;
    }

    /**
     * Get the field definition for a given field ID.
     *
     * @param Template $template
     * @param int $fieldId
     * @return ?TemplateField
     */
    private static function getField(Template $template, int $fieldId): ?TemplateField
    {
        // Find the field definition to match this field data
        $field = $template->fields->firstWhere('id', $fieldId);

        if (!$field) {
            Log::warning('Missing field definition for template_field_id: ' . $fieldId);
            return null;

            // TODO: Handle missing field definition with versioning
        }

        return $field;
    }
}