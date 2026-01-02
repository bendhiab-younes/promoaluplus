<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'city',
        'project_type',
        'description',
        'budget_range',
        'timeline',
        'attachments',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
