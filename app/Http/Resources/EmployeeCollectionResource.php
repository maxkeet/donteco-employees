<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
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

        if ($this->resource instanceof LengthAwarePaginator) {
            $meta = [
                'total' => $this->resource->total(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'next_page_url' => $this->resource->nextPageUrl() ? $this->generatePageUrl($request, $this->resource->currentPage() + 1) : null,
                'prev_page_url' => $this->resource->previousPageUrl() ? $this->generatePageUrl($request, $this->resource->currentPage() - 1) : null,
            ];
        } else {
            $meta = [
                'count' => $this->collection->count(),
            ];
        }


        return [
            'employees' => $this->collection->map(function ($employee) {
                return [
                    'test' => $employee->id,
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
            'meta' => $meta
        ];
    }

    protected function generatePageUrl(Request $request, $page) : string
    {
        return url()->current() . '?' . http_build_query(['name' => $request->query('name'), 'per_page' => $this->resource->perPage(), 'page' => $page]);
    }
}
