<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryInfo extends Model
{
    protected $table = 'salary_infos';
    protected $fillable = ['salary_or_hourly', 'typical_hours', 'annual_salary', 'hourly_rate'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
