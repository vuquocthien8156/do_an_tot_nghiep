<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchStaff extends Model {
    protected $table = 'branch_staff';

    public function nameStaff() {
        return $this->hasOne(Users::class, 'id', 'staff_id');
    }
}