<?php

namespace App\Repositories;

use App\Interfaces\EmployeeRepositoryInterface;
use App\Models\Employee;
use Illuminate\Support\Collection;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAllByName(string $name) : Collection {

        return Employee::select('employees.name', 'job_titles.*', 'salary_infos.*')
            ->join('job_titles', 'employees.job_title_id', '=', 'job_titles.id')
            ->join('salary_infos', 'employees.salary_info_id', '=', 'salary_infos.id')
            ->where('employees.name', $name)
            ->get();

    }
}
