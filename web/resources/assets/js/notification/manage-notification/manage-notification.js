'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({
    el: '#manage-notification',
    components: { Pagination},
    data() {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            results: {},
            id_partner_field: '',
            list_partner:'',
        };
    },
    created() { 
        this.listNotification()
    },
    methods: {
        listNotification(page) {
            var data = {
                _token: this.csrf,
            };
            if (page) {
                data.page = page;
            }
            common.loading.show('body');
            $.post('/notification/search-notification', data)
                .done(response => {
                    this.results = response;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        getPartner() {
            var data = {
                id_partner_field: this.id_partner_field,
            }
            $('#all').val(data.id_partner_field);
            common.loading.show('body');
            $.get('partner', data)
                .done(response => {
                    this.list_partner = response;
                }).fail(error => {
                    bootbox.alert('Error!!!');
                }).always(() => {
                    common.loading.hide('body');
                });
        },
        formatDate(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return  date.getFullYear() + "-" + (date.getMonth()+1) + "-" +  date.getDate() + " " + strTime;
        },
        deleteNotification(id) {
            var data = {
                id_notification_schedule:id
            };
            bootbox.confirm({
                    title: 'Thông báo',
                    message: 'Bạn muốn xóa thông báo này không này không?',
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
                            $.ajax({
                                url: 'delete-notification',
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                type: 'POST',
                                data: data,
                                success: function (result) {
                                    if (result.error === 0) {
                                        common.loading.hide('body');
                                        bootbox.alert("Xoá thành công!!", function() {
                                            window.location = '/notification/manage';
                                        })
                                    } else {
                                        common.loading.hide('body');
                                        bootbox.alert('Có lỗi vui lòng thử lại sau.');
                                    }
                                },
                                error: function () {
                                    bootbox.alert('Có lỗi vui lòng thử lại sau.');
                                }
                            });
                        }
                    }
                });
        },
        submitForm : function(event) {
            event.preventDefault();
            var time_send = $('#time_send').val();
            var date_send = $('#date_send').val();
            var date_time = new Date(date_send + ' ' + time_send);
            var time_ = date_time.toUTCString().replace('GMT', '');
            var format_time = new Date(time_);
            var time_config = this.formatDate(format_time);
            $('#time_config').val(time_config);
            
            var content_notification = $('#content_notification').val().trim();
            if(content_notification == "" || content_notification == null) {
                bootbox.alert('Hãy nhập nội dung!');
                return false;
            }
            var group_customer = $('#group-customer').val();
            var customer_of_unit = $('#partner_field').val();
            if ($('#group-customer-specifically').is(':checked') == true && 
                (group_customer == ''  && group_customer== null)) {
                bootbox.alert('Hãy chọn đơn vị');
                return false;
            }
            if ($('#group-customer-specifically').is(':checked') == true && 
                (group_customer != ''  && group_customer != null) && 
                (customer_of_unit == ''  && customer_of_unit== null)) {
                bootbox.alert('Hãy chọn đơn vị');
                return false;
            }
            common.loading.show('body');

            $.ajax({
                url: $('#form_notification').attr("action"),
                method: 'POST',
                data: $('#form_notification').serialize(),
                success: function(response) {
                    if (response.error === 0) {
                        common.loading.hide('body');
                        bootbox.alert("Lưu thành công !", function() {
                            window.location = window.location.pathname;
                        })
                    } else {
                        common.loading.hide('body');
                        //bootbox.alert(response.message);
                    }
                },
                error: function() {
                    common.loading.hide('body');
                    bootbox.alert('Lỗi !!');
                }
            });
        }
    }
});
