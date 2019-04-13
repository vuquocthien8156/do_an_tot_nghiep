<?php

namespace App\Repositorys;


use App\Models\UserRole;
use App\Repositories\BaseRepository;

class UserRoleRepository extends BaseRepository {
	public function __construct(UserRole $model) {
		$this->model = $model;
	}

	public function getUserRoles(int $user_id) {
		return $this->model->where('user_id', $user_id)->get();
	}
}