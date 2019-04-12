@extends('layout.base')

@section('body-content')
    <div id="chat-user3"> 
        <div class="row mt-5 pt-3">
        </div>
        {{-- <h3 class="text-left mb-3">MESSAGING</h3> --}}
        <h3 class="mb-3"> <a class="tag-title-show pb-3" style="text-decoration: none;" href="#"> <b> MESSAGING </b>  </a></h3>
        <div class="messaging">
            <div class="inbox_msg">
                <div class="inbox_people">
                    <div class="headind_srch">
                        <div class="recent_heading">
                            <h4>Recent</h4>
                        </div>
                        <div class="srch_bar">
                            <div class="stylish-input-group">
                                {{-- <input type="text" class="search-bar" id="search-bar-user" placeholder="Search"> --}}
                                {{-- <select class="search-bar" id="user_modal" @change="checkUserIdHasConveration()" style="width: 90%">
                                    @foreach ($infoCustomer as $item)
                                        @if ($item->phone != null && $item->phone != '')
                                            <option value="{{$item->id}}--{{$item->name}}">
                                                {{ $item->name }} / {{ $item->phone }}
                                            </option> 
                                        @endif
                                    @endforeach
                                </select> --}}
                                <select class="search-bar" id="user_modal" style="width: 90%">
                                   
                                </select>
                                <span class="input-group-addon">
                                    <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                </span> 
                            </div>
                        </div>
                    </div>
                    <div class="inbox_chat" id="div_data_user" v-cloak>
                        {{-- array for new user --}}
                        <div class="chat_list" v-if="chats_new_user.length > 0" v-for="(item, index) in chats_new_user" :class="{'active_chat' : index == 0}"  @click="getMessages(null, false)">
                            <div class="chat_people">
                                <div class="chat_img"> 
                                    <img :src="item.avatar_path ||'/images/user.png'" class="rounded-circle" style="width: 28.5px; height: 28.5px"> 
                                </div>
                                <div class="chat_ib">
                                    <h5>@{{item.name_conversation}}
                                        <span class="message_not_seen rounded-circle bg-danger text-white"> </span>
                                        <span class="chat_date">@{{item.time_last_message}}</span>
                                    </h5>
                                    <p>@{{item.last_messages}}</p>
                                </div>
                            </div>
                        </div>
                        {{-- array has conversation --}}
                        <div class="chat_list" v-for="(item, index) in chats" :key="item.conversation_id" :class="{'active_chat' : index == 0}" :id="item.conversation_id" @click="getMessages(item.conversation_id, true); getMembers(item.conversation_id);">
                            <div class="chat_people">
                                <div class="chat_img"> 
                                    <img :src="item.avatar_path ||'/images/user.png'" class="rounded-circle" style="width: 28.5px; height: 28.5px"> 
                                </div>
                                <div class="chat_ib">
                                    <h5>@{{item.name_conversation}}
                                        <span :id="'number_message_not_seen_'+ item.conversation_id" class="message_not_seen rounded-circle bg-danger text-white"> </span>
                                        <span class="chat_date">@{{item.time_last_message}}</span>
                                    </h5>
                                    <p>@{{item.last_messages}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="loading_image_user align-middle d-none" style="justify-content: center;">
                            <img src="https://www.voya.ie/Interface/Icons/LoadingBasketContents.gif" width="50px" height="50px" />
                        </div>
                    </div>
                </div>
                <div class="mesgs">
                    <div class="msg_history" v-cloak>
                        <div class="loading_image_messages align-middle d-none" style="justify-content: center;">
                            <img src="https://www.voya.ie/Interface/Icons/LoadingBasketContents.gif" width="50px" height="50px" />
                        </div>
                        <div v-for="(item, index) in messages">
                            <div class="outgoing_msg" v-if="item.from_user_id == admin_id">
                                <div class="sent_msg">
                                    <div class="received_withd_msg">
                                        <p v-if="item.message_type == 1">@{{item.message}} </p>
                                        <p v-else-if="item.message_type == 2" class="bg-white p-0">
                                            <img v-for="(img, ind) in item.message" :src="path_resouce + '/' + img" :id="ind" class="image_chat pr-2" :class="{'float-right' : item.message.length == 1}" style="width: 50%; height: 60%; background-size: cover;"> <br>
                                            <span class="d-block font-weight-bold" :class="item.receiver_resource_class" v-if="item.message.length == 1" :style="style_Image_Chat"> @{{item.receiver_resource_action}} </span>
                                            <span class="d-block font-weight-bold" :class="item.receiver_resource_class" v-else> @{{item.receiver_resource_action}} </span>
                                        </p>
                                        <span class="time_date" v-if="item.message_type == 2" :style="style_Image_Chat"> @{{item.time_chat}} </span>
                                        <span class="time_date" v-else>@{{item.time_chat}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="incoming_msg" v-else>
                                <div class="incoming_msg_img"><img src="/images/user.png" class="rounded-circle" style="max-width: 75%; width: 30px; height: 30px;"> </div>
                                <div class="received_msg">
                                    <div class="received_withd_msg">
                                        <p v-if="item.message_type == 1">@{{item.message}}</p>
                                        <p v-else-if="item.message_type == 2" class="bg-white p-0">
                                            <img v-for="(img, index) in item.message" :src="path_resouce + '/' + img" :id="index" class="image_chat pr-2" style="width: 50%; height: 60%; background-size: cover;"> <br>
                                        </p>
                                        <span class="time_date">@{{item.time_chat}}</span> 
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
                            <input type="text" id="content_chat" class="write_msg pr-5 pl-5" placeholder="Type a message" />
                            <button class="msg_send_btn" @click="sendMessage()" type="button"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="text" class="d-none" id="path_resouce" value="{{$path_resource}}" hidden>
            <input class="d-none" type="file" @change="handleUploadImage" id="image_upload" data-id_admin="{{auth()->id()}}" name="image_upload" accept="image/*" multiple>
        </div>
        <div id="myModal_chat" class="modal_chat">
            <span class="close_chat" style="top: 100px">&times;</span>
            <img class="modal-content-chat h-100" id="img01">
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-database.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.7.2/firebase-storage.js"></script>
    <script type = "application/javascript">
        var app = firebase.initializeApp(JSON.parse('{!! json_encode(config('app.firebase')) !!}'));
        var database = firebase.database();
        var storage = firebase.storage(); 
        
        
    </script>
    <script type="text/javascript">
        @php
            include public_path('/js/conversation/chat-user/chat-user3.js');
        @endphp
    </script>
    <script type="text/javascript">
    </script>
@endsection
