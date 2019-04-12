@extends('layout.base')
@section('body-content')
	<div id="test"> 
			user <input type="text" v-model="user" name="user"><br>
			pass <input type="password" v-model="pass" name="pass"><br>
			<button @click="dangnhap()">Gữi</button>
	</div>
@endsection
@section('scripts')
	<script type="text/javascript">
		@php
			include public_path('/js/logintest/login/login.js');
		@endphp
	</script>
@endsection