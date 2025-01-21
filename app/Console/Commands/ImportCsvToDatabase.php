<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\JobTitle;
use App\Models\SalaryInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportCsvToDatabase extends Command
{
    const EXPECTED_CSV_HEADERS = ['Name', 'Job Titles', 'Department', 'Full or Part-Time', 'Salary or Hourly', 'Typical Hours', 'Annual Salary', 'Hourly Rate'];
    const VALID_FULL_OR_PART_TIME = ['F', 'P'];
    const VALID_SALARY_OR_HOURLY = ['SALARY', 'HOURLY'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv_to_db {file : path to CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a set of CSV dates into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->error("File not found or not readable: $filePath");
            return 1;
        }

        $file = fopen($filePath, 'r');

        $headers = fgetcsv($file);
        if (!$headers || $headers !== self::EXPECTED_CSV_HEADERS) {
            $this->error("Unexpected CSV headers. Expected '" . implode(",", self::EXPECTED_CSV_HEADERS) . "'");
            fclose($file);
            return 1;
        }

        // Check data validity
        $rowNum = 1;
        while (($row = fgetcsv($file)) !== false) {
            $rowNum++;
            $fullOrPartTime = $row[3];
            $salaryOrHourly = $row[4];
            $typicalHours = $row[5];

            // Check validity of 'Full or Part-Time'
            if ($fullOrPartTime !== '' && !in_array($fullOrPartTime, self::VALID_FULL_OR_PART_TIME)) {
                $this->error("Invalid 'Full or Part-Time' value: $fullOrPartTime in row $rowNum");
                fclose($file);
                return 1;
            }

            // Check validity of 'Salary or Hourly'
            if (!in_array($salaryOrHourly, self::VALID_SALARY_OR_HOURLY)) {
                $this->error("Invalid 'Salary or Hourly' value: $salaryOrHourly in row $rowNum");
                fclose($file);
                return 1;
            }

            // Check validity of 'Typical Hours'
            if ($typicalHours !== '' && !ctype_digit($typicalHours)) {
                $this->error("Invalid value in the field 'Typical Hours'(Most be integer or empty): $typicalHours in row $rowNum");
                fclose($file);
                return 1;
            }
        }

        rewind($file);
        fgetcsv($file);

        // add progressbar for DB inserting
        $progressBar = new ProgressBar($this->output, $rowNum);
        $progressBar->start();

        // duplicate rows counter
        $duplicate_count = 0;
        $duplicate_rows = "";
        $insertedEmployeesCount = 0;

        DB::beginTransaction();
        try {
            $rowNum = 1;
            while (($row = fgetcsv($file)) !== false) {
                $rowNum++;
                $name = $row[0];
                $jobTitle = $row[1];
                $department = $row[2];
                $fullOrPartTime = $row[3] !== '' ? $row[3] : null;;
                $salaryOrHourly = $row[4];
                $typicalHours = $row[5] !== '' ? (int)$row[5] : null;
                $annualSalary = $row[6] !== '' ? (float)$row[6] : null;
                $hourlyRate = $row[7] !== '' ? (float)$row[7] : null;

                $jobTitleModel = JobTitle::firstOrCreate([
                    'job_title' => $jobTitle,
                    'department' => $department
                    ]);

                $salaryInfoModel = SalaryInfo::firstOrCreate([
                    'salary_or_hourly' => $salaryOrHourly,
                    'typical_hours' => $typicalHours,
                    'annual_salary' => $annualSalary,
                    'hourly_rate' => $hourlyRate,
                ]);

                // check duplicates
                try {
                    Employee::create([
                        'name' => $name,
                        'full_or_part_time' => $fullOrPartTime,
                        'job_title_id' => $jobTitleModel->id,
                        'salary_info_id' => $salaryInfoModel->id,
                    ]);
                    $insertedEmployeesCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() === '23000') { // MySQL unique constraint code
                        $first = Employee::where('name', $name)
                            ->where('job_title_id', $jobTitleModel->id)
                            ->where('salary_info_id', $salaryInfoModel->id)->first();

                        // check duplicates
                        // if duplicates partial rollback transaction because we don't, which of two rows import from dataset
                        if($first->full_or_part_time != $fullOrPartTime){
                            DB::rollBack();
                            $this->error("\n Duplicate row $rowNum with value of the field 'Full or Part-Time' in the file does not match the value in the database: " . $e->getMessage());
                            return 1;
                        // if duplicates full just inform about it
                        } else {
                            $duplicate_rows .= $rowNum . ", ";
                            $duplicate_count++;
                        }
                    } else {
                        throw $e;
                    }
                }

                // advance progressbar
                $progressBar->advance(); // Обновление прогресса
            }
            DB::commit();

            $progressBar->finish();
            $this->info("\n Processed (" . $rowNum-1 ." rows)");
            $this->info("Inserted (" . $insertedEmployeesCount ." employees)");
            if($duplicate_count) $this->info("Skipped $duplicate_count duplicated rows: " . trim($duplicate_rows, ", "));
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n Error importing data(row: $rowNum): " . $e->getMessage());
        } finally {
            fclose($file);
        }

        return 0;
    }
}
