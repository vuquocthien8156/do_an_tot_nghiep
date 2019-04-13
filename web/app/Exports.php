<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class BookingExport implements FromCollection {

    public function collection() {
        $result = DB::table('service_registration')
                    ->select('svr.created_at as timebooking', 'svr.id', 'svr.service_start_at', 'svr.total', 'svr.selected_payment_method', 'svr.payment_received', 'us.name as username')
                    ->from('service_registration as svr')
                    ->join('users as us', 'svr.customer_id', '=', 'us.id')
                    ->get()->toArray();          
        return $result;
    }
}