<?php

namespace App\Exports;
use App\Models\JobHistoryModel;
use Doctrine\DBAL\Schema\Index;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Request;

class JobsHistoryExport implements ShouldAutoSize, FromCollection, WithHeadings, WithMapping{
    public function collection() {
        $request = Request::all();
        return JobHistoryModel::getRecord($request);
    }

    protected $index = 0;

    public function map($user):array {
        $startDate  = date('d-m-Y', strtotime($user->start_date));
        $endDate    = date('d-m-Y', strtotime($user->end_date));
        if ($user->department_id == 1){
            $department = 'UI/UX Designer';
        }
           $department = 'Backend Developer';
        $createdAtFormat = date('d-m-Y H:i A', strtotime($user->created_at));
        return[
            $user->id,
            $user->name.' '.$user->last_name,
            $startDate,
            $endDate,
            $user->job_title,
            $department,
            $createdAtFormat,
        ];
    }

    public function headings():array {
        return[
            'Table ID',
            'Employee Name',
            'Start Date',
            'End Date',
            'Job Title',
            'Department',
            'Created At'
        ];
    }
}





?>
