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
use App\Enums\EAppointmentType;
use Illuminate\Support\Carbon;
use App\Enums\EDateFormat;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;

class AppointmentExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait;
    
    public function __construct($username_phone_number, $type_appointment, $branch,  $from_date, $to_date)
    {
        $this->username_phone_number = $username_phone_number;
        $this->type_appointment = $type_appointment;
        $this->branch = $branch;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }


    public function collection() {

        $result = DB::table('appointment as appo')
                    ->select('us.name as name_user', 'us.phone', 'appo.type', 'appo.appointment_at', 'bra.name as name_branch', 'appo.enable_reminder', 'appo.note')
                    ->join('users as us', 'us.id', '=', 'appo.user_id')
                    ->join('branch as bra', 'bra.id', '=', 'appo.branch_id');
            
        if ($this->username_phone_number != '' && $this->username_phone_number != "null") {
            $result->where(function($where) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($this->username_phone_number, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($this->username_phone_number, 'UTF-8')) . '%']);
              });
        }
        if ($this->type_appointment != '' && $this->type_appointment != "null") {
            $result->where('appo.type', '=', $this->type_appointment);
        }
        if ($this->branch != '' && $this->branch != "null") {
            $result->where('appo.branch_id', '=', $this->branch);
        } 
        if ($this->from_date != '' && $this->from_date != "null") {
            $result->where('appo.appointment_at', '>', $this->from_date);
        }
        if ($this->to_date != '' && $this->to_date != "null") {
            $result->where('appo.appointment_at', '<', $this->to_date);
        }
        $result = $result->where([
            ['us.status', '=', EStatus::ACTIVE],
            ['appo.status', '=', EStatus::ACTIVE]
        ])->orderBy('appo.id', 'desc')->get();

        $timezone = $this->getUserTimezone();
        foreach ($result as $key => $item) {
            if ($item->enable_reminder == true) {
                $item->enable_reminder = "Có";
            } else {
                $item->enable_reminder = "Không";
            }
            $item->appointment_at =  Carbon::parse($item->appointment_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT);
            $item->type = EAppointmentType::valueToName($item->type);
        }
        return $result;
    }

    public function headings(): array {
        return [
            'TÊN KHÁCH HÀNG',
            'SỐ ĐIỆN THOẠI',
            'LOẠI LỊCH HẸN',
            'THỜI GIAN',
            'CHI NHÁNH',
            'NHẮC NHỞ',
            'GHI CHÚ'
        ];
    }
}

