@extends('layouts.after_login')

@section('title', 'Readingtohabit-プロフィール編集の情報入力')

@php
    if (empty($errors)) {
        $errors = [];
    }
@endphp

<div id="edit_profile">
    @isset($dialog)
    <div id="dialog">{{$dialog}}</div>
    <div v-if="dialog === true" class="show_dialog">{{$dialog}}</div>
    @endisset
    
    <div v-if="delete_modal" class="modal_area">
        <div v-if="delete_modal_form" class="delete_modal">
            <div class="close_btn_area">
                <div class="close_btn" v-on:click="close_delete_modal()"><i class="fas fa-times close_white"></i></div>
            </div>
            <div class="p_2rem">
                <div class="confirm_delete">
                    退会するとデータは全て削除され、元に戻せなくなります。<br>
                    よろしいでしょうか。
                </div>
                <div class="btn_vertical_align_horizon_between">
                    <button class="btn_danger_less_than_4_chars mr_3" v-on:click="delete_user_do()">はい</button>
                    <button class="btn_default_less_than_4_chars" v-on:click="close_delete_modal()">いいえ</button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="smaller === true" class="wrapper" v-on:mousemove="close_dialog()" v-on:scroll="close_dialog()">
        <div class="content">
            @include('components.edit_user_head', ['edit_target' => 'profile'])

            <form action="edit_profile" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                @include('components.edit_profile_input', ['input' => 'profile_img', 'profile' => $profile, 'errors' => $errors])
                @include('components.edit_profile_input', ['input' => 'name', 'profile' => $profile, 'errors' => $errors])
                @include('components.edit_profile_input', ['input' => 'email', 'profile' => $profile, 'errors' => $errors])
                <input type="submit" class="btn_primary_less_than_4_chars" value="更新する">
            </form>
            
            <div class="boundary_border"></div>
            
            <button v-on:click="open_delete_modal()" class="btn_danger_less_than_4_chars">退会する</button>
        </div>

        @include('components.footer_nav', ['current_page' => 'edit_profile'])

    </div>

    <div v-if="larger === true" v-on:mousemove="close_dialog()" v-on:scroll="close_dialog()">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'edit_profile'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="p_after_login_large">
                        <div class="content_title">プロフィール編集</div>
                        <form action="edit_profile" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            @include('components.edit_profile_input', ['input' => 'profile_img', 'profile' => $profile, 'errors' => $errors])
                            @include('components.edit_profile_input', ['input' => 'name', 'profile' => $profile, 'errors' => $errors])
                            @include('components.edit_profile_input', ['input' => 'email', 'profile' => $profile, 'errors' => $errors])
                            <input type="submit" class="btn_primary_less_than_4_chars" value="更新する">
                        </form>

                        <div class="boundary_border"></div>

                        <button v-on:click="open_delete_modal()" class="btn_danger_less_than_4_chars">退会する</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
