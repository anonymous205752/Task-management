<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',  // Add due_date here
    ];

    protected $casts = [
        'due_date' => 'datetime',  // Cast due_date as a datetime
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
