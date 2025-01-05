<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateField extends Model
{
    /** @use HasFactory<\Database\Factories\TemplateFieldFactory> */
    use HasFactory;

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
