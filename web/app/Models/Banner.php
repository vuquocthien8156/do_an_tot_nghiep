<?php
namespace App\Models;

class Banner extends BaseModel {
	protected $table = 'banner';

	public function getActionOnClickTargetAttribute($value) {
		return empty($value) ? null : json_decode($value);
	}

	public function setActionOnClickTargetAttribute($value) {
		if (!empty($value)) {
			$this->attributes['action_on_click_target'] = json_encode($value);
		}
	}
}