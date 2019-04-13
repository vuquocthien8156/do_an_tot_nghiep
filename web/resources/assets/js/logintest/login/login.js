'use strict';

import * as Pagination from 'laravel-vue-pagination';

const app = new Vue({

    el: '#test',
    components: {Pagination},
    data() {
        return {
            user:'',
            pass:'',
            user1:'',
            pass1:'',
            sdt:'',
            ns:'',
            name:'',
            gioitinh:'',
        };
    },
    methods: {
        dangnhap() {
            if (this.user == '' || this.user == null || this.pass == '' || this.pass == null) {
                alert("vui lòng nhập đủ thông tin")
            }
            else
            {
                var data = {
                user:this.user,
                pass:this.pass
            }
            $.post('dangnhap', data)
                .done(response => {
                    console.log(response.a == '');
                    if (response.a == '') {
                        alert('Thất bại');
                    }
                    else
                    {
                        alert('thành công');
                    }    
                })
                .fail(error => {
                    alert('Error!');
                }).always(() => {
                    common.loading.hide('body');
                });    
            }
        	
        },
        dangky() {
            if (this.user1 == '' || this.user1 == null || this.pass1 == '' || this.pass1 == null || 
                this.sdt == '' || this.sdt == null || this.gioitinh == '' || this.gioitinh == null ||
                this.ns == '' || this.ns == null) {
                alert('vui lòng nhập đủ thông tin');
            }
            else
            {
                var data = {
                    user:this.user1,
                    pass:this.pass1,
                    sdt:this.sdt,
                    gioitinh:this.gioitinh,
                    ns:this.ns,
                    name:this.name
                }
                $.post('dangky', data)
                    .done(response => {
                        if (response == 1) {
                            alert('Thất bại');
                        }
                        else
                        {
                        alert('thành công');
                         window.location = '/login';
   
                        }    
                    })   
                }
        },
	}
});
