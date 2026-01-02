<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;
    protected $table = 'issues';
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'type',
        'status',
        'priority'
    ];

    public function project()
    {
        return $this->belongTo(Project::class, 'project_id');
    }
}
