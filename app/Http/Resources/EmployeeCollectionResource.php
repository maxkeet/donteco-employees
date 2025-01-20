<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EmployeeCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'employees' => $this->collection->map(function ($employee) {
                return [
                    'name' => $employee->name,
                    'job_title' => $employee->job_title,
                    'department' => $employee->department,
                    'full_or_part_time' => $employee->full_or_part_time,
                    'salary_or_hourly' => $employee->salary_or_hourly,
                    'typical_hours' => $employee->typical_hours,
                    'annual_salary' => $employee->annual_salary,
                    'hourly_rate' => $employee->hourly_rate
                ];
            }),
            'meta' => [
                'total' => $this->collection->count(),
            ],
        ];
    }
}
