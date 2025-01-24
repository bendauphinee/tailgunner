<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateRecordValue extends Model
{
    /** @use HasFactory<\Database\Factories\TemplateRecordValueFactory> */
    use HasFactory;

    // Add template field type to db column mapping
    private const TYPE_TO_COLUMN_MAP = [
        'integer' => 'integer_value',
        'string' => 'string_value',
        'text' => 'text_value',
        'dropdown' => 'string_value',
        'checkbox' => 'string_value'
    ];

    protected $fillable = [
        'template_id',
        'template_field_id',
        'template_record_id',
        'integer_value',
        'string_value',
        'text_value'
    ];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function field()
    {
        return $this->belongsTo(TemplateField::class, 'template_field_id');
    }

    public function record()
    {
        return $this->belongsTo(TemplateRecord::class, 'template_record_id');
    }

    /**
     * Get database column name for storing template field type.
     */
    public static function getColumnForType(string $fieldType): string
    {
        return self::TYPE_TO_COLUMN_MAP[$fieldType] ?? 'string_value';
    }

    /**
     * Get value based on field type
     */
    public function getTypedValue(string $fieldType): mixed
    {
        $column = self::getColumnForType($fieldType);
        return $this->$column;
    }

    /**
     * Set value based on field type
     */
    public function setTypedValue(string $fieldType, mixed $value): void
    {
        $column = self::getColumnForType($fieldType);
        $this->$column = $value;
    }
}