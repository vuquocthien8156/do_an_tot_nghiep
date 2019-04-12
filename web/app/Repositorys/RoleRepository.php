<?php

namespace App\Repositorys;


use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository {
	public function __construct(Role $model) {
		$this->model = $model;
	}

	public function getByCode(string $code) {
		return $this->model->where('code', $code)->first();
	}
}