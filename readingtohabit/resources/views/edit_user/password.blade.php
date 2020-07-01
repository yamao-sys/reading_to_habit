@extends('layouts.after_login')

@section('title', 'Readingtohabit-パスワード編集の情報入力')

@php
    if (empty($errors)) {
        $errors = [];
    }
@endphp

<div id="edit_password">
    @isset($dialog)
    <div id="dialog">{{$dialog}}</div>
    <div v-if="dialog === true" class="show_dialog">{{$dialog}}</div>
    @endisset
    <div v-if="smaller === true" class="wrapper" v-on:mousemove="close_dialog()" v-on:scroll="close_dialog()">
        <div class="content">
            @include('components.edit_user_head', ['edit_target' => 'password'])

            <form action="edit_password" method="post">
                {{ csrf_field() }}
                @include('components.edit_password_input', ['input' => 'current_password', 'errors' => $errors])
                @include('components.edit_password_input', ['input' => 'new_password', 'errors' => $errors])
                @include('components.edit_password_input', ['input' => 'new_password_to_check', 'errors' => $errors])
                <input type="submit" class="btn_primary_less_than_4_chars" value="更新する">
            </form>
        </div>

        @include('components.footer_nav', ['current_page' => 'edit_password'])

    </div>

    <div v-if="larger === true" v-on:mousemove="close_dialog()" v-on:scroll="close_dialog()">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'edit_password'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="p_after_login_large">
                        <div class="content_title">パスワード編集</div>
                        <form action="edit_password" method="post">
                            {{ csrf_field() }}
                            @include('components.edit_password_input', ['input' => 'current_password', 'errors' => $errors])
                            @include('components.edit_password_input', ['input' => 'new_password', 'errors' => $errors])
                            @include('components.edit_password_input', ['input' => 'new_password_to_check', 'errors' => $errors])
                            <input type="submit" class="btn_primary_less_than_4_chars" value="更新する">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
