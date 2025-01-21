<?php

namespace App\Interfaces;

interface EmployeeRepositoryInterface
{
    public function getAllByFullName(string $name);
    public function searchByName(string $name, int $limit);
}
