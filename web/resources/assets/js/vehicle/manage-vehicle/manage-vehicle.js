'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({

    el: '#manage-vehicle',
    components: {Pagination},
    data() {
        return {
            results_search: {},
            results_image: {},
            result_infoExport:{},
            poster: '',
            status: '',
            manufacture:'',
            manufacture_model_results: {},
            id_manufacture: '',
            model: '',
            checkApprove:[],
            code: '',
            checkCensorship: [],
        };
    },

	created() {
		this.searchVehicle();
	},

	filters: {
		descriptionSubstr:function(string) {
			if (string == null) {
				return 'Chưa có mô tả';
			}
			if (string == '') {
				return 'Chưa có mô tả';
			}
			return string.substring(0,80)
		},

		formatDescription:function(string) {
			return string;
		}
	},

    methods: {
        searchVehicle(page) {
            var data = {
                poster: this.poster,
                status: this.status,
                model:this.model,
                id_manufacture:this.id_manufacture,
                code: this.code,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('manage/search', data)
                .done(response => {
                    this.results_search = response.listSearch;
                    this.result_infoExport = response.exportVehicleList;    
                })
                .fail(error => {
                    bootbox.alert('Error!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },

		checkprioritize(selling_id,displayOrder){
			var $input;
			var data = {
				selling_id : selling_id,
				displayOrder : displayOrder
			};
			bootbox.confirm({
				title: 'Thông báo',
				message: 'Bạn muốn thay đổi độ ưu tiên?',
				buttons: {
					confirm: {
						label: 'Ưu tiên sản phẩm',
						className: 'btn-primary',
					},
					cancel: {
						label: 'Hủy bỏ',
						className: 'btn-default'
					}
				},
				callback: (result) => {
					if (result) {
						common.loading.show('body');
						$.post('manage/updateprioritize',data)
						.done(response =>{
							if (response.error === 0) {
								common.loading.hide('body');
								bootbox.alert("Ưu tiên thành công !!", function() {
									window.location = '/vehicle/manage';
								})
							} else {
								bootbox.alert('error!!!');
							}
						})
						.fail(error => {
							bootbox.alert('Error!');
						 })
						.always(() => {
							common.loading.hide('body');
						});
					}
				}					
			});		
		},

        getModelManufacture() {
            if (this.id_manufacture === 'null') {
                this.id_manufacture = null;
            }
            var data = {
                id_manufacture: this.id_manufacture,
            };
            common.loading.show('body');
            $.post('manage/manufacture/model', data)
                .done(response => {
                    this.manufacture_model_results = response;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },

		updateStatus(selling_id){
			var data = {
				selling_id:selling_id
			}
			bootbox.confirm({
				title: 'Thông báo',
				message: 'Cập nhật trạng thái xe thành Đã bán',
				buttons: {
					confirm: {
						label: 'Xác nhận',
						className: 'btn-primary',
					},
					cancel: {
						label: 'Bỏ qua',
						className: 'btn-default'
					}
				},
				callback: (result) => {
					if (result) {
						common.loading.show('body');
						$.post('manage/updateStatus',data)
						.done(response => {
							window.location.reload();
							alert('Cập nhật trạng thái thành công');
						})
						.fail(error => {
								bootbox.alert('Error!');
						})
						.always(() => {
								common.loading.hide('body');
						});
					} else {
                        $('#check_selling'+selling_id).prop('checked', false);
                    }
				}
			});
		},

		loadSellingRequestResource(selling_id) {
			var data = {
				id:selling_id
			};
			common.loading.show('body');
			$.post('manage/resource', data)
				.done(response => {
					this.results_image = response;
				}).fail(error => {
					bootbox.alert('Error!');
				}).always(() => {
					common.loading.hide('body');
				});
		},

		getModelManufacture() {
			if (this.id_manufacture === 'null') {
				this.id_manufacture = null;
			}
			var data = {
				id_manufacture: this.id_manufacture,
			};
			common.loading.show('body');
			$.post('manage/manufacture/model', data)
				.done(response => {
					this.manufacture_model_results = response;
				}).fail(error => {
					bootbox.alert('Error!!!');
				}).always(() => {
					common.loading.hide('body');
				});
		},

		approveSellingRequest() {
			if (this.checkApprove == null || this.checkApprove == '') {
				alert('Vui lòng chọn đơn hàng cần duyệt !');
			} else {
				var data = {
					approved: this.checkApprove,
				}
				bootbox.confirm({
					title: 'Thông báo',
					message: 'Bạn muốn duyệt đơn hàng này không?',
					buttons: {
						confirm: {
							label: 'Xác nhận',
							className: 'btn-primary',
						},
						cancel: {
							label: 'Bỏ qua',
							className: 'btn-default'
						}
					},
					callback: (result) => {
						if (result) {
							common.loading.show('body');
							$.post('manage/update-approved', data)
							.done(response => {
								if (response.error === 0) {
									common.loading.hide('body');
									bootbox.alert("Duyệt thành công !!", function() {
										window.location = '/vehicle/manage';
									})
								} else {
									bootbox.alert('Error!!!');
								}
							}).fail(error => {
								bootbox.alert('Error!!!');
							}).always(() => {
								common.loading.hide('body');
							});
						}
					}
				});
			}        
		},

		checkaccredited() {
			if (this.checkCensorship == null || this.checkCensorship == '') {
				alert('Vui lòng chọn các kiểm định cần duyệt !');
			} else {
				var data = {
					accredited: this.checkCensorship,
				};
				bootbox.confirm({
					title: 'Thông báo',
					message: 'Cập nhật trạng thái thành Đã kiểm duyệt',
					buttons: {
						confirm: {
							label: 'Xác nhận',
							className: 'btn-primary',
						},
						cancel: {
							label: 'Bỏ qua',
							className: 'btn-default'
						}
					},
					callback: (result) => {
						if (result) {
							common.loading.show('body');
							$.post('manage/update-accredited', data)
							.done(response => {
								if (response.error === 0) {
									common.loading.hide('body');
									bootbox.alert("Cập nhật thành công !!", function() {
										window.location.reload();
									})
								} else {
									bootbox.alert('Error!!!');
								}
							}).fail(error => {
								bootbox.alert('Error!!!');
							}).always(() => {
								common.loading.hide('body');
							});
						} else {
							$(this).prop('checked','checked');
						}
					}
				});
			}
		},
	}
});
