@extends('layouts.default')

@section('title', 'Readingtohabitユーザー登録情報入力')

@section('content')
<div class="register_user_content">
    <div class="content_title">ユーザー登録情報入力</div>
    <div class="register_user_form">
        <form action="register_user_check" method="post">
            @empty ($errors)
                @php
                $errors = array();
                @endphp
            @endempty
            {{ csrf_field() }}
            @include('components.register_user_form_input', ['input' => 'name', 'register_user_info' => $register_user_info, 'errors' => $errors])
            @include('components.register_user_form_input', ['input' => 'email', 'register_user_info' => $register_user_info, 'errors' => $errors])
            @include('components.register_user_form_input', ['input' => 'password', 'register_user_info' => $register_user_info, 'errors' => $errors])
            @include('components.register_user_form_input', ['input' => 'password_to_check', 'register_user_info' => $register_user_info, 'errors' => $errors])

            <div class="caution">
                登録することによって、<a href="rules" class="link_primary">利用規約</a>・<a href="plivacy_policy" class="link_primary">プライバシーポリシー</a>に同意しているものとみなします。
            </div>
        
            <div class="submit_btn">
                <input type = "submit" class="btn_primary_more_than_4_chars" value="登録情報確認画面へ">
            </div>

            <div class="to_login">
                ログインは<a href="login" class="link_primary">こちら</a>から
            </div>
        </form>
    </div>
</div>
@endsection
