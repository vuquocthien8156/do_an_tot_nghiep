$(document).ready(function() {
	setTimeout(function() {
		$('body').on('click', '.see_more_less', function() { 
			var id = $(this).data('id');
			index(id);
		});
		$('body').on('click', '.see_image', function() { 
			$("#imageVehicle").show();
        	$("#cavetVehicle").show();
		});
		
	},2000);       
	function index(id) {
		if( $('#description' + id).hasClass('d-none')) {
			$('#description' + id).removeClass('d-none');
			$('#description' + id).addClass('d-block');
			$('#eclip' + id).removeClass('d-inline');
			$('#eclip' + id).addClass('d-none');
			$('#descriptionSubstr' + id).removeClass('d-block');
			$('#descriptionSubstr' + id).addClass('d-none');
			$('#see_more_less' + id).text('Thu gọn');
		} else {
			$('#description' + id).removeClass('d-block');
			$('#description' + id).addClass('d-none');
			$('#descriptionSubstr' + id).removeClass('d-none');
			$('#descriptionSubstr' + id).addClass('d-block');
			$('#eclip' + id).removeClass('d-none');
			$('#eclip' + id).addClass('d-inline');
			$('#see_more_less' + id).text('Xem thêm mô tả');
		}
	};  
}); 