<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /** @use HasFactory<\Database\Factories\TemplateFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fields()
    {
        return $this->hasMany(TemplateField::class);
    }

    public function records()
    {
        return $this->hasMany(TemplateRecord::class);
    }

    /**
     * Get template with field information.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $withExtendedFields Include all commonly fields.
     * @param bool $withAllFields Include all fields.
     */
    public function scopeWithMetaAndFields($query, $withExtendedFields = false, $withAllFields = false)
    {
        // The base fields
        $fieldsInfo = [
            'id',
            'template_id',
            'label',
            'name',
            'type',
        ];

        // Get the commonly used extended options
        if ($withExtendedFields || $withAllFields) {
            $fieldsInfo = array_merge(
                $fieldsInfo,
                ['order', 'extended_options']
            );
        }

        // Get everything
        if ($withAllFields) {
            $fieldsInfo = array_merge(
                $fieldsInfo,
                ['created_at', 'updated_at']
            );
        }

        return $query
            ->select(['id', 'title', 'description'])
            ->with([
                'fields' => function($query) {
                    $query->select([
                        'id',
                        'template_id',
                        'label',
                        'name',
                        'type',
                        'order',
                        'extended_options',
                    ])
                    ->orderBy('order');
                }
            ]);
    }
}
