@extends('layout.base')

@section('body-content')
    <div id="chat-user"> 
        <div class="row mt-5 pt-3">
        </div>
        <h3 class=" text-center">Messaging</h3>
        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch">
                        <div class="recent_heading">
                            <h4>Recent</h4>
                        </div>
                        <div class="srch_bar">
                            <div class="stylish-input-group">
                                <input type="text" class="search-bar" id="search-bar-user" onkeyup="filterSearchUser()" placeholder="Search">
                                {{-- <input type="text" class="search-bar" id="search-bar-user" v-on:keyup="filterSearchUser()" placeholder="Search" > --}}

                                <span class="input-group-addon">
                                    <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </span> 
                            </div>
                        </div>
                    </div>
                    <div class="inbox_chat" id="div_data_user" v-if="data_user.length > 0" v-cloak>
                        <div class="chat_list" v-for="(item, index) in data_user" :class="{'active_chat' : index == 0}" v-if="item.id_user != 1" @click="getDataConversationFirebase(item.id_user, item.avatar)">
                            <div class="chat_people">
                                <div class="chat_img" v-if="item.check_path_avatar_user == 1"> 
                                    <img :src="item.avatar" class="rounded-circle" style="width: 28.5px; height: 28.5px"> 
                                </div>
                                <div class="chat_img" v-else-if="item.check_path_avatar_user == 2"> 
                                    <img :src="{{"'" . $path_resource . "'"}} + '/' + item.avatar" class="rounded-circle" style="width: 28.5px; height: 28.5px"> 
                                </div>
                                <div class="chat_img" v-else> 
                                    <img src="/images/user.png" class="rounded-circle" style="width: 28.5px; height: 28.5px"> 
                                </div>
                                <div class="chat_ib">
                                    <h5> @{{ item.name }} 
                                        <span class="message_not_seen rounded-circle bg-danger text-white" :id="'number_ms_not_seen_' + item.id_user" v-for="mesage_not_seen in array_user_number_mesage_not_seen" v-if="mesage_not_seen.id_user == item.id_user && mesage_not_seen.number_mesage_not_seen > 0"> 
                                            @{{mesage_not_seen.number_mesage_not_seen}} 
                                        </span>
                                        <span class="chat_date" :id="'chat_date_time_' + item.id_user"></span>
                                    </h5>
                                    <p :id="'old_message_' + item.id_user"></p>
                                </div>
                            </div>
                        </div>
                        <div class="loading_image_user align-middle d-none" style="justify-content: center;">
                            <img src="https://www.voya.ie/Interface/Icons/LoadingBasketContents.gif" width="50px" height="50px" />
                        </div>
                        {{-- <div class="d-flex align-middle" style="justify-content: center;">
                            <a onclick="loadMore()" id="load_more_user" class="btn btn-primary button-app d-inline" style="border-radius: 15px">Load more</a>
                        </div> --}}
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history" v-cloak>
                        <div class="loading_image_messages align-middle d-none" style="justify-content: center;">
                            <img src="https://www.voya.ie/Interface/Icons/LoadingBasketContents.gif" width="50px" height="50px" />
                        </div>
                        <div v-for="(item, index) in messages">
                            
                            <div class="incoming_msg" v-if="item.from == id_user">
                                <div class="incoming_msg_img" v-if="check_path_avatar == 1"> <img :src="{{"'" . $path_resource . "'"}} + '/' + image_user" class="rounded-circle" style="max-width: 75%; width: 30px; height: 30px;"> </div>
                                <div class="incoming_msg_img" v-else-if="check_path_avatar == 0"> <img :src="image_user" class="rounded-circle" style="max-width: 75%; width: 30px; height: 30px;"> </div>
                                <div class="incoming_msg_img" v-else> <img src="/images/user.png" class="rounded-circle" style="max-width: 75%; width: 30px; height: 30px;"> </div>
                                <div class="received_msg">
                                    <div class="received_withd_msg">
                                        <p v-if="item.content != null"> 
                                            @{{item.content}} 
                                        </p>
                                        <p v-else-if="item.images != undefined" class="bg-white p-0">
                                            <img v-for="(img, index) in item.images" :src="img" :id="index" class="image_chat pr-2" style="width: 50%; height: 60%; background-size: cover;"> <br>
                                        </p>
                                        <p v-else> </p>
                                        <span class="time_date"> @{{item.date_time_chat}} </span> 
                                    </div>
                                </div>
                            </div>
                            <div class="outgoing_msg" v-else>
                                <div class="sent_msg">
                                    <div class="received_withd_msg">
                                        <p v-if="item.content != null"> 
                                            @{{item.content}} 
                                        </p>
                                        <p v-else-if="(item.images != undefined) && (item.action == 1)" class="bg-white p-0">
                                            <img v-for="(img, index) in item.images" :src="img" :id="index" class="image_chat pr-2" :class="{'float-right' : item.images.length == 1}" style="width: 50%; height: 60%; background-size: cover;"> <br>
                                            <span class="text-success d-block font-weight-bold" :id="item.key" v-if="item.images.length == 1" :style="style_Image_Chat"> ĐÃ ĐỒNG Ý </span>
                                            <span class="text-success d-block font-weight-bold" :id="item.key" v-else> ĐÃ ĐỒNG Ý </span>
                                        </p>

                                        <p v-else-if="(item.images != undefined) && (item.action == 0)" class="bg-white p-0">
                                            <img v-for="(img, index) in item.images" :src="img" :id="index" class="image_chat pr-2" :class="{'float-right' : item.images.length == 1}" style="width: 50%; height: 60%; background-size: cover;"> <br>
                                            <span class="text-danger d-block font-weight-bold" :id="item.key" v-if="item.images.length == 1" :style="style_Image_Chat"> ĐÃ TỪ CHỐI </span>
                                            <span class="text-danger d-block font-weight-bold" :id="item.key" v-else> ĐÃ TỪ CHỐI </span>
                                        </p>

                                        <p v-else-if="(item.images != undefined) && (item.action == null)" class="bg-white p-0">
                                            <img v-for="(img, index) in item.images" :src="img" :id="index" class="image_chat pr-2" :class="{'float-right' : item.images.length == 1}" style="width: 50%; height: 60%; background-size: cover;"> <br>
                                            <span class="text-primary d-block font-weight-bold" :id="item.key" v-if="item.images.length == 1" :style="style_Image_Chat"> CHƯA XÁC NHẬN </span>
                                            <span class="text-primary d-block font-weight-bold" :id="item.key" v-else> CHƯA XÁC NHẬN </span>
                                        </p>
                                        <span class="time_date" v-if="(item.images != undefined) && (item.images.length == 1)" :style="style_Image_Chat"> @{{item.date_time_chat}} </span>
                                        <span class="time_date" v-else> @{{item.date_time_chat}} </span>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="loading_image position-relative d-none" style="border: none; margin-left: 41rem">
                            <img src="/images/Loading_icon.gif" width="80px" height="80px" />
                        </div>
                    </div>
                    <div class="type_msg">
                        <div class="input_msg_write">
                            <label for="image_upload" class="msg_upload_btn" style="background: none; color: #05728f;"><i class="far fa-image" aria-hidden="true"></i></label>
                            <input type="text" class="write_msg pr-5 pl-5" v-model="content_chat" placeholder="Type a message" />
                            <button class="msg_send_btn" @click="pushContentChat()" type="button"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <input class="d-none" type="file" id="image_upload" data-id_admin="{{auth()->id()}}" :data-id_user="id_user" :data-key_conversation="key_conversation" name="image_upload" accept="image/*" multiple>
        </div>
        <div id="myModal_chat" class="modal_chat">
            <span class="close_chat" style="top: 100px">&times;</span>
            <img class="modal-content-chat h-100" id="img01">
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-database.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-storage.js"></script>
    <script type = "application/javascript">
        var app = firebase.initializeApp(JSON.parse('{!! json_encode(config('app.firebase')) !!}'));
        var database = firebase.database();
        var storage = firebase.storage(); 

        
        setTimeout(function() {
            $(".chat_list").on("click", function() {
                $(".chat_list").removeClass("active_chat");
                $(this).addClass("active_chat");
            });

            var fileUpload = document.getElementById("image_upload");

            fileUpload.addEventListener('change', function(evt) {
                var arr_image = [];
                var id_admin = $('#image_upload').data('id_admin');
                var id_user = $('#image_upload').data('id_user');
                var key_conversation = $('#image_upload').data('key_conversation');
                common.checkFirebaseReady().then(() => {
                    $('.loading_image').addClass('d-block');
                    $('.msg_history').animate({
                            scrollTop: $('.msg_history').get(0).scrollHeight
                    }, 500);
                    for (var i = 0; i < evt.target.files.length; i++) {
                        var date_time = new Date().getTime();
                        var firstFile = evt.target.files[i];
                        var metadata = { contentType: evt.target.files[i].type };
                        var uploadTask = firebase.storage().ref(id_admin + '/' + date_time).put(firstFile, metadata)
                                                .then(snapshot => snapshot.ref.getDownloadURL())
                                                .then(url => arr_image.push(url));
                    }
                }).done(function() {
                    setTimeout(function() {
                        if(arr_image.length > 0) {
                            var date_ = new Date().getTime();
                            firebase.database().ref('conversations/' + key_conversation + '/messages/' + date_).set({
                                from: id_admin,
                                images: arr_image,
                                is_seen: false,
                            });
                            firebase.database().ref('conversations/' + key_conversation).update({
                                updated_at: date_,
                            });
                            firebase.database().ref('conversations/' + key_conversation).update({
                                last_message: '[Hình ảnh]'
                            });
                            $('.msg_history').animate({
                                scrollTop: $('.msg_history').get(0).scrollHeight
                            }, 800);
                            $('.loading_image').removeClass('d-block');
                            $('.loading_image').addClass('d-none');
                        } else {
                            $('.loading_image').removeClass('d-block');
                            $('.loading_image').addClass('d-none');
                        }
                    }, 4000);
                });
            });
        }, 5000);
    </script>
    <script type="text/javascript">
        @php
            include public_path('/js/conversation/chat-user/chat-user2.js');
        @endphp
    </script>
    <script type="text/javascript">
        var getList = 10;
        function filterSearchUser() {
            var input, filter, a, i;
            input = document.getElementById("search-bar-user");
            filter = input.value.toUpperCase();
            div = document.getElementById("div_data_user");
            a = div.getElementsByTagName("h5");
            for (i = 0; i < a.length; i++) {
                txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].parentElement.parentElement.parentElement.style.display = "";
                } else {
                    a[i].parentElement.parentElement.parentElement.style.display = "none";
                }
            }
        }
        $(document).ready(function() {
            setTimeout(function() {
                var div = document.getElementById("div_data_user");
                var a = div.getElementsByTagName("h5");
                for (i = 0; i < a.length; i++) {
                    if (i <= getList) {
                        a[i].parentElement.parentElement.parentElement.style.display = "";
                    } else {
                        a[i].parentElement.parentElement.parentElement.style.display = "none";
                    }
                }
                scrollLoadMoreUser();
            }, 3000);
            $(".search-bar").focusin(function() {
                $(".search-bar").css('width', '97%')
            });
            $(".search-bar").focusout(function() { 
                $(".search-bar").css('width', '60%')
            });
        });
        function loadMore() {
            $('.loading_image_user').removeClass('d-none');
            $('.loading_image_user').addClass('d-flex');
            setTimeout(function() {
                getList = getList + 5;
                var div = document.getElementById("div_data_user");
                var a = div.getElementsByTagName("h5");
                if (getList >= a.length) {
                    $('#load_more_user').removeClass('d-inline');
                    $('#load_more_user').addClass('d-none');
                }
                for (i = 0; i < a.length; i++) {
                    if (i < getList) {
                        a[i].parentElement.parentElement.parentElement.style.display = "";
                    } else {
                        a[i].parentElement.parentElement.parentElement.style.display = "none";
                    }
                }
                $('.loading_image_user').removeClass('d-flex');
                $('.loading_image_user').addClass('d-none');
            }, 3000);
            
        }
        function scrollLoadMoreUser() {
            var lastScrollTop = 0;
            $('#div_data_user').scroll(function(event){
            var st = $(this).scrollTop();
            if (st > lastScrollTop){
                loadMore();
            }
            lastScrollTop = st;
            });
        }
    </script>
@endsection