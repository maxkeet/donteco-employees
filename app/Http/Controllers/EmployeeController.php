<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Resources\EmployeeCollectionResource;
use App\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Http\Request;

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
    public function getEmployersByName(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $employees = $this->employeeRepositoryInterface->getAllByName($request->route('name'));
            if($employees->count() > 0) {
                return ApiResponseClass::sendResponse(new EmployeeCollectionResource($employees),'',200);
            }

            return ApiResponseClass::sendResponse([],'Employees not found',404);

        } catch (\Exception $e) {
            return ApiResponseClass::throw($e,$e->getMessage());
        }
    }
}
