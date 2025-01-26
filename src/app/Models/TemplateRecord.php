<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateRecord extends Model
{
    /** @use HasFactory<\Database\Factories\TemplateRecordFactory> */
    use HasFactory;

    protected $fillable = ['template_id'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function values()
    {
        return $this->hasMany(TemplateRecordValue::class);
    }
}