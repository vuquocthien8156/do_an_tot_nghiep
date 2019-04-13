<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Services\VehicleService;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Facades\DB;
use App\Enums\EStatus;
use App\Enums\EUser;
use Illuminate\Support\Carbon;
use App\Enums\EDateFormat;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Enums\EVehicleStatus;
use App\Enums\EVehicleAccredited;
use App\Enums\EVehicleType;

class VehicleExport implements FromCollection, WithHeadings, ShouldAutoSize {
    use CommonTrait, Exportable ;

    public function __construct($poster, $id_manufacture_selling, $selling_status, $model)
    {
        $this->poster_selling = $poster;
        $this->id_manufacture_selling = $id_manufacture_selling;
        $this->selling_status = $selling_status;
        $this->model = $model;
       
    }

    public function collection() {
        
        $result = DB::table('selling_vehicle as sv')
            ->join('users as us', 'us.id', '=', 'sv.seller_id')  
            ->join('category as cat', 'cat.id', '=', 'sv.vehicle_manufacture_id')
            ->select('sv.vehicle_manufacture_id', 'sv.vehicle_model_id', 'sv.price', 'us.name as poster_name',
            'us.phone', 'sv.name as title', 'sv.description', 'sv.status as selling_status', 'sv.accredited');

        if ($this->id_manufacture_selling != null) {
            $result->where('sv.vehicle_manufacture_id', '=', $this->id_manufacture_selling);
        }

        if ($this->model != '' && $this->model != "null") {
            $result->where('sv.vehicle_model_id', '=', $this->model);
        }
     
        if ($this->poster_selling != '' && $this->poster_selling != "null") {
             $result->where(function($where) {
                $where->whereRaw('lower(us.name) like ? ', ['%' . trim(mb_strtolower($this->poster_selling, 'UTF-8')) . '%'])
                      ->orWhereRaw('lower(us.phone) like ? ', ['%' . trim(mb_strtolower($this->poster_selling, 'UTF-8')) . '%']);
                });
        }

        if ($this->selling_status != '' &&  $this->selling_status != "null") {
            $result->where('sv.status', '<>', EStatus::DELETED);
        }

        if ( $this->selling_status != '' &&  $this->selling_status != "null") {
            $result->where('sv.status', '=',  $this->selling_status);
        }

        $result = $result->orderBy('sv.id', 'desc')->get();
        foreach ($result as $key => $item) {
            $getNameManufactureById = VehicleRepository::getNameManufactureByIdExport($item->vehicle_manufacture_id);
            $getNameManufactureModalById = VehicleRepository::getNameManufactureByIdExport($item->vehicle_model_id);
            $item->vehicle_manufacture_id = isset($getNameManufactureById[0]->name) ? $getNameManufactureById[0]->name : '';
            $item->vehicle_model_id = isset($getNameManufactureModalById[0]->name) ? $getNameManufactureModalById[0]->name : '';
            $item->selling_status = EVehicleStatus::getStatusString($item->selling_status);
            $item->accredited = EVehicleAccredited::getAccredited($item->accredited);
        }
        return $result;
    }

    public function headings(): array {
        return [
            'Hãng xe',
            'Dòng xe',
            'Giá',
            'Người đăng',
            'Số điện thoại',
            'Tiêu đề bài đăng',
            'Mô Tả',
            'Trạng Thái',
            'Tình Trạng',
        ];
    }
}

