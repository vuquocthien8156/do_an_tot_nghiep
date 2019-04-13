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
use Maatwebsite\Excel\Concerns\Exportable;


class CustomerExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait;

    public function __construct($username_phone, $from_date, $to_date, $status, $partner_id)
        {
            $this->username_phone = $username_phone;
            $this->from_date = $from_date;
            $this->to_date = $to_date;
            $this->status = $status;
            $this->partner_id = $partner_id;
        }

    public function collection() {
        $result = DB::table('users as us')->select('us.name', 'us.phone', 'us_ad.address', 'us.date_of_birth', 'us.created_at', 'us.partner_id', 'us.status')
                    ->leftJoin('user_address as us_ad', 'us_ad.user_id', '=', DB::raw('us.id and us_ad.is_default = true'))
                    ->where('us.type', '=', EUser::TYPE_USER);
        if ($this->username_phone != '' && $this->username_phone != "null") {
        $result->where(function($where) {
            $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($this->username_phone, 'UTF-8')) . '%'])
                  ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($this->username_phone, 'UTF-8')) . '%'])
                  ->orWhereRaw('lower(us.email) like ? ', ['%' . trim(mb_strtolower($this->username_phone, 'UTF-8')) . '%']);
            });
        }
        if ($this->from_date != "null") {
            $result->where('us.created_at', '>', $this->from_date);
        }

        if ($this->to_date != '' && $this->to_date != "null") {
            $result->where('us.created_at', '<', $this->to_date);
        }

        if ($this->status != '' && $this->status != "null") {
            $result->where('us.status', '=',$this->status );
        } else {
            $result->where('us.status', '<>', EStatus::DELETED);
        }

        if ($this->partner_id != '' && $this->partner_id != "null") {
            $result->where('us.partner_id', '=', $this->partner_id);
        }
        $result = $result->orderBy('us.id', 'desc')->get(); 

        $timezone = $this->getUserTimezone();
        foreach ($result as $key => $item) {
            $item->created_at =  ($item->created_at != null) ? Carbon::parse($item->created_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT) : '';
            $item->date_of_birth = ($item->date_of_birth != null && $item->date_of_birth != '') ? Carbon::parse($item->date_of_birth)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : '';
            $item->status = EUser::valueToName($item->status);
            $item->partner_id = isset($item->partner_id) ? (CustomerService::getPartnerNameExcel($item->partner_id))[0]->name : null;
        }
        return $result;
    }

    public function headings(): array {
        return [
            'TÊN KHÁCH HÀNG',
            'SỐ ĐIỆN THOẠI',
            'ĐỊA CHỈ',
            'NGÀY SINH',
            'NGÀY TẠO',
            'ĐƠN VỊ',
            'TRẠNG THÁI'
        ];
    }
}

