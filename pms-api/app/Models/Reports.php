<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'type',
        'status',
        'priority',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
