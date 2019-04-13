<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {
	protected $dateFormat = 'Y-m-d H:i:s.u O';

	public function getCreatedAtAttribute($value) {
		return DateUtility::tryParsedDateFromFormat($value);
	}

	public function getUpdatedAtAttribute($value) {
		return DateUtility::tryParsedDateFromFormat($value);
	}

	public function getDeletedAtAttribute($value) {
		return DateUtility::tryParsedDateFromFormat($value);
	}
}