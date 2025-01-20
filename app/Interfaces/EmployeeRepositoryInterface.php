<?php

namespace App\Interfaces;

interface EmployeeRepositoryInterface
{
    public function getAllByName(string $name);
}
