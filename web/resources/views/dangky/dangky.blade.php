@extends('layout.base')
@section('body-content')
	<div style="margin-bottom: 50px;">
		
	</div>
	<div id="test"> 
			Tên<input type="text" v-model="name" name=""><br>
			SDT<input type="text" v-model="sdt" name=""><br>
			Giới tính
			<select v-model="gioitinh">
				<option value="Nam">Nam</option>
				<option value="Nu">Nữ</option>	
			</select><br>
			Ngày Sinh<input type="date" v-model="ns" name=""><br>
			Tên Đăng nhập<input type="text" v-model="user1" name=""><br>
			Mật khẩu<input type="password" v-model="pass1" name=""><br>
			<button @click="dangky()">Đăng ký</button>
	</div>
@endsection
@section('scripts')
	<script type="text/javascript">
		@php
			include public_path('/js/logintest/login/login.js');
		@endphp
	</script>
@endsection