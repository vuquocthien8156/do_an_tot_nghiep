<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model {
    protected $table = 'branch';
    public $timestamps = false;

    public function branch_staffs() {
        return $this->hasMany(BranchStaff::class, 'branch_id', 'id');
    }

    public function branch_staffs2() {
        return $this->hasManyThrough(Users::class, BranchStaff::class, 'branch_id', 'id', 'staff_id');
    }
}