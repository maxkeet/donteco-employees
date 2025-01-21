<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\EmployeeCollectionResource;
use App\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    private EmployeeRepositoryInterface $employeeRepositoryInterface;

    public function __construct(EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
        $this->employeeRepositoryInterface = $employeeRepositoryInterface;
    }

    /**
     * Display the specified resource.
     */
    public function searchByName(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'per_page' => 'int|min:10|max:100'
        ], [
            'name.required' => 'The name field is required',
            'name.string' => 'The name field must be string',
            'name.min' => 'The name field must be at least 3 characters',
            'name.max' => 'The name field may not be greater than 255 characters',
            'per_page.int' => 'The per_page field must be integer',
            'per_page.min' => 'The per_page field must be at least 10',
            'per_page.max' => 'The per_page field may not be greater than 100',
        ]);
        if($validator->fails()){
            return ApiResponseClass::validateErrors($validator->errors());
        }
        $validator->validate();

        try {
            //limit from request, but min 10 and max 100
            $limit = max(min($request->input('per_page', 50), 100), 10);
            $employees = $this->employeeRepositoryInterface->searchByName($name = $request->query('name'), $limit);
            if($employees->count() > 0) {
                return ApiResponseClass::sendResponse(new EmployeeCollectionResource($employees),'',200);
            }

            return ApiResponseClass::sendResponse([],'Employees not found',404);

        } catch (\Exception $e) {
            return ApiResponseClass::throw($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getByFullName(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $employees = $this->employeeRepositoryInterface->getAllByFullName($request->route('name'));
            if($employees->count() > 0) {
                return ApiResponseClass::sendResponse(new EmployeeCollectionResource($employees),'',200);
            }

            return ApiResponseClass::sendResponse([],'Employees not found',404);

        } catch (\Exception $e) {
            return ApiResponseClass::throw($e, $e->getMessage());
        }
    }
}
