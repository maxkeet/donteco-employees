<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $fillable = ['name', 'job_title_id', 'salary_info_id', 'full_or_part_time'];

    public function jobTitle(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id', 'id');
    }

    public function salaryInfo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SalaryInfo::class, 'salary_info_id', 'id');
    }
}
