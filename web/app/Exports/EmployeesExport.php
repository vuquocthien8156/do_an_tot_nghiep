<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Services\CustomerService;
use Illuminate\Support\Facades\DB;
use App\Enums\EStatus;
use App\Enums\EUser;
use Illuminate\Support\Carbon;
use App\Enums\EDateFormat;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;

class EmployeesExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait;

    public function __construct($name_phone_email, $branch_id, $status, $type_employees)
    {
        $this->name_phone_email = $name_phone_email;
        $this->branch_id = $branch_id;
        $this->status = $status;
        $this->type_employees = $type_employees;
    }


    public function collection() {

        $result = DB::table('branch_staff as bs')
                    ->select('us.name', 'us.phone', 'us.email', 'us.date_of_birth', 'br.name as branch_name',  'ct.name as category_name')
                    ->join('users as us', 'us.id', '=', 'bs.staff_id')
                    ->join('branch as br', 'br.id', '=', 'bs.branch_id')
                    ->join('category as ct', 'us.staff_type_id', '=', 'ct.id')
                    ->where('us.status', '=', EStatus::ACTIVE);
        if ($this->name_phone_email != '' && $this->name_phone_email != 'null') {
            $result->where(function($where) { 
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($this->name_phone_email, 'UTF-8')) . '%'])
                    ->orWhereRaw('lower(us.email) like ? ', ['%' . trim(mb_strtolower($this->name_phone_email, 'UTF-8')) . '%'])
                    ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($this->name_phone_email, 'UTF-8')) . '%']);
            });
        } 
        if ($this->branch_id != '' && $this->branch_id != 'null') {
            $result->where('bs.branch_id', '=', $this->branch_id);
        }
        if ($this->status != '' && $this->status != 'null') {
            $result->where('bs.status', '=', $this->status);
        } else {
            $result->where('bs.status', '!=', EStatus::DELETED);
            $result->where('br.status', '!=', EStatus::DELETED);
            $result->where('ct.status', '!=', EStatus::DELETED);
        }
        if ($this->type_employees != '' && $this->type_employees != 'null') {
            $result->where('us.staff_type_id', '=', $this->type_employees);
        }
        $result = $result->orderBy('us.id', 'desc')->get();
        
        $timezone = $this->getUserTimezone();
        return $result;
    }

    public function headings(): array {
        return [
            'TÊN NHÂN VIÊN',
            'SỐ ĐIỆN THOẠI',
            'EMAIL',
            'NGÀY SINH',
            'CHI NHÁNH',
            'LOẠI NHÂN VIÊN'
        ];
    }
}

