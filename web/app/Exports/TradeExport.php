<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Services\VehicleService;
use App\Repositories\TradeRepository;
use Illuminate\Support\Facades\DB;
use App\Enums\EStatus;
use App\Enums\EOrderType;
use App\Enums\EUser;
use Illuminate\Support\Carbon;
use App\Enums\EDateFormat;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleAccredited;
use App\Enums\EVehicleType;

class TradeExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait, Exportable ;

    public function __construct($from_date, $employees, $to_date, $customer_phone, $vehicle_number, $name_store)
    {
        $this->customer_phone = $customer_phone;
        $this->from_date = $from_date;
        $this->employees = $employees;
        $this->to_date = $to_date;
        $this->vehicle_number = $vehicle_number;
        $this->name_store = $name_store;
    }

    public function collection() {
        
      $result = DB::table('orders')
                    ->select('br.name as branch_user', 'us.name as user_name', 'us.phone as user_phone','od.name as description', 'orders.code as code', 'orders.vehicle_number as vehicle_number', 'orders.price', 'use.name', 'orders.odo_km as kilometer' ,'orders.created_at as order_created_at', 're.content as feedback')
                    ->join('order_detail as od', 'od.order_id', '=', 'orders.id')
                    ->join('users as us', 'us.id', '=', 'orders.user_id')  
                    ->join('users as use', 'use.id', '=','orders.completed_by')
                    ->leftJoin('review as re', 're.table_id', '=', 'orders.id')
                    ->leftJoin('branch_staff as bs', 'us.id', '=','bs.staff_id')
                    ->leftJoin('branch as br', 'br.id', '=', 'orders.branch_id');


       if ($this->customer_phone != '' && $this->customer_phone != 'null') {
            $result->where(function($where){
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($this->customer_phone, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($this->customer_phone, 'UTF-8')) . '%']);
                });
        } 

        if ($this->employees != '' && $this->employees != 'null') {
            $result->where(function($where) use ($employees) {
                $where->whereRaw('lower(use.name) like ? ', ['%' . trim(mb_strtolower($this->employees, 'UTF-8')) . '%']);
            });
        }

        if ($this->vehicle_number != '' && $this->vehicle_number != 'null') {
            $result->where(function($where) use ($vehicle_number) {
                $where->whereRaw('lower(orders.vehicle_number) like ? ', ['%' . trim(mb_strtolower($this->vehicle_number, 'UTF-8')) . '%']);
            });
        }

        if ($this->name_store != '' && $this->name_store != 'null') {
            $result->where('br.id', '=', $this->name_store);
        }

        if ($this->from_date != '' && $this->from_date != 'null') {
            $result->where('orders.created_at', '>', $this->from_date);
        }

        if ($this->to_date != '' && $this->to_date != 'null') {
            $result->where('orders.created_at', '<', $this->to_date);
        }

        $result = $result->where([['od.type', '=', EOrderType::ARBITRARY_SERVICE_ORDER]])->orderBy('orders.id', 'desc')->get();
        
        $timezone = $this->getUserTimezone();
        foreach ($result as $key => $item) {
            $item->order_created_at =  Carbon::parse($item->order_created_at)->timezone($timezone)->format(EDateFormat::MODEL_DATE_FORMAT_DEFAULT);
        }

        return $result;
    }

    public function headings(): array {
        return [
            'CỬA HÀNG',
            'TÊN KHÁCH HÀNG',
            'SỐ ĐIỆN THOẠI',
            'NỘI DUNG',
            'MÃ PHIẾU THU',
            'BIỂN SỐ XE',
            'TỔNG TIỀN',
            'NHÂN VIÊN',
            'SỐ KM',
            'NGÀY TẠO',
            'PHẢN HỒI'

        ];
    }
}

