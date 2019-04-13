<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Services\CustomerService;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;
use App\Enums\EStatus;
use App\Enums\EUser;
use Illuminate\Support\Carbon;
use App\Enums\EDateFormat;
use App\Enums\ECardMemberType;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;

class MemberCardExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait; 
    public function __construct($username_phone_number_vehicle, $manufacture, $model, $code, $status, $approved, $vehicle_card_status)
    {
        $this->username_phone_number_vehicle = $username_phone_number_vehicle;
        $this->manufacture = $manufacture;
        $this->model = $model;
        $this->code = $code;
        $this->status = $status;
        $this->approved = $approved;
        $this->vehicle_card_status = $vehicle_card_status;
    }
    public function collection() {
        $result = DB::table('membership_card as ms')
                    ->select('us.name', 'us.phone', 'ms.name as name_card', 'ms.vehicle_number', 'ms.code', 'ms.created_at', 'ms.approved_at', 'ms.expired_at', 'ms.code', 'ms.vehicle_card_status', 'ms.status', 'ms.approved')
                    ->join('users as us', 'us.id', '=', 'ms.user_id');
            
        if ($this->username_phone_number_vehicle != '' && $this->username_phone_number_vehicle != "null") {
            $result->where(function($where) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($this->username_phone_number_vehicle, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($this->username_phone_number_vehicle, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(ms.vehicle_number) like ? ', ['%' . trim(mb_strtolower($this->username_phone_number_vehicle, 'UTF-8')) . '%']);
            });
        }

        if ($this->manufacture != '' && $this->manufacture != "null") {
            $result->where('ms.vehicle_manufacture_id', '=', $this->manufacture);
        }
        if ($this->model != '' && $this->model != "null") {
            $result->where('ms.vehicle_model_id', '=', $this->model);
        }
        if ($this->code != '' && $this->code != "null") {
            $result->where('ms.code', 'like', '%' . $this->code . '%');
        }
        if ($this->approved != '' && $this->approved != 'null') {
            $result->where('ms.approved', '=', $this->approved);
        }
        if ($this->vehicle_card_status != '' && $this->vehicle_card_status != 'null') {
            $result->where('ms.vehicle_card_status', '=', $this->vehicle_card_status);
        }
        if ($this->status != '' && $this->status != "null") {
            $result->where('ms.status', '=', $this->status);
        } else {
            $result->where('ms.status', '<>', EStatus::DELETED);
            $result->where('ms.status', '<>', 2);
        }
        $result = $result->where([
            ['us.status', '=', EStatus::ACTIVE]
        ])->orderBy('ms.id', 'desc')->get();
        foreach ($result as $key => $item) {
            if ($item->status == EStatus::DELETED) {
                $item->vehicle_card_status = 'Đã Xoá';
            } else if ($item->approved == true) {
                $item->vehicle_card_status = 'Đã kích hoạt';
            } else if ($item->approved == false && $item->vehicle_card_status == 1) {
                $item->vehicle_card_status = 'Đã đăng ký';
            } else {
                $item->vehicle_card_status = 'Chưa đăng ký';
            }
            $item->created_at = ($item->created_at != null && $item->created_at != '') ? Carbon::parse($item->created_at)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : '';
            $item->approved_at = ($item->approved_at != null && $item->approved_at != '') ? Carbon::parse($item->approved_at)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : '';
            $item->expired_at = ($item->expired_at != null && $item->expired_at != '') ? Carbon::parse($item->expired_at)->format(EDateFormat::MODEL_DATE_FORMAT_NORMAL) : '';

        }
        return $result;
    }

    public function headings(): array {
        return [
            'TÊN THÀNH VIÊN',
            'SỐ ĐIỆN THOẠI',
            'TÊN TRÊN THẺ',
            'BIỂN SỐ XE',
            'MÃ THẺ THÀNH VIÊN',
            'NGÀY ĐĂNG KÝ',
            'NGÀY HIỆU LỰC',
            'NGÀY HẾT HẠN',
            'TRẠNG THÁI'
        ];
    }
}

