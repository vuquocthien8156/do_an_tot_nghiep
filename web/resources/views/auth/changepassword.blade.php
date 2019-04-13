@extends('layout.base')
@section('stylesheet')
@endsection
@section('body-content')
<div id="changepassword">
	<div class="row mt-5 pt-3 pb-5">
		<div style="padding-left: 2rem">
			<h4 class="tag-page-custom">
				<a class="tag-title-show" style="text-decoration: none;" href="{{route('view-change-password', [], false)}}"> ĐỔI MẬT KHẨU </a>
			</h4>
		</div>
	</div>
	<div class="container pl-0 pr-0 pb-5">
		<div class="w-100" style="min-height: 150px">
			<div class="form-box col-12 m-auto">
				<div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 33rem;">
					<form method="POST" action="change-password" class="form-inline" enctype="multipart/form-data" onSubmit="return checkPw(this)"> 
						@csrf
						<div class="form-group w-100 mb-3">
							<label for="email_employees" class="col-md-4 p-1 justify-content-start align-items-start font-weight-bold"> Email </label>
							<label for="email_employees" class="col-md-8 p-1 justify-content-start align-items-start font-weight-bold">
								{{ str_limit(auth()->user()->email, 20) }}
							</label>
						</div>
						<div class="form-group w-100 mb-3">
							<label for="newpassword_employees" class="col-md-4 p-1 justify-content-start align-items-start font-weight-bold"> Tên </label>
							<input type="text" id="name_employees" value="{{ str_limit(auth()->user()->name, 255) }}" name="name_employees" class="form-control" style="width: 285px" placeholder="Nhập tên bạn muốn đổi" required>
						</div>
						<div class="form-group w-100 mb-0">
							<label for="currentpassword_employees" class="col-md-4 p-1 justify-content-start align-items-start font-weight-bold"> Nhập mật khẩu cũ </label>
							<input type="password" id="currentpassword_employees" name="currentpassword_employees" class="form-control" style="width: 285px" placeholder="Nhập mật khẩu cũ" required>
							@if (isset($error))
								<div class="form-group w-100 mb-3">
									<label class="col-12 ml-2" style="color: red;text-align: right;"> Mật khẩu cũ không đúng </label>
								</div>
							@endif

						</div>
						<div class="form-group w-100 mt-3 mb-3">
							<label for="newpassword_employees" class="col-md-4 p-1 justify-content-start align-items-start font-weight-bold"> Nhập mật khẩu mới </label>
							<input type="password" id="newpassword_employees" name="newpassword_employees" class="form-control" style="width: 285px" placeholder="Nhập mật khẩu mới" required>
						</div>
						<div class="form-group w-100 mb-0">
							<label for="repeatnewpassword_employees" class="col-md-4 p-1 justify-content-start align-items-start font-weight-bold"> Nhập lại mật khẩu mới </label>
							<input type="password" id="repeatnewpassword_employees" name="repeatnewpassword_employees" class="form-control" style="width: 285px" placeholder="Nhập lại mật khẩu mới" required>
						</div>
						<div class="form-group w-100 mb-3">
							<label id="wiewerror" class="col-12 ml-4" style="color: red;text-align: right;"> </label>
						</div>
						<div class="row">
							<button type="submit" class="button-app" style="margin: 15px 0 10px 135px"> Đổi mật khẩu </button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
	function checkPw(form) {
		pw1 = form.newpassword_employees.value;
		pw2 = form.repeatnewpassword_employees.value;
		div = 'Nhập lại mật khẩu không đúng';
		if (pw1 != pw2) {
			$('#wiewerror').html(div);
		return false;
		}
		else return true;
		}
</script>
@endsection