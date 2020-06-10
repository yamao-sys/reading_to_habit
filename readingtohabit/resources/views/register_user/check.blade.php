@extends('layouts.default')

@section('title', 'Readingtohabitユーザー登録情報確認')

@section('content')
<div class="register_user_content">
    <div class="content_title">ユーザー登録情報確認</div>
    <div class="register_user_check_area">
        <form action="register_user_do" method="post">
        {{ csrf_field() }}
            @include('components.register_user_form_check', ['input' => 'name', 'user_info' => $user_info])
            @include('components.register_user_form_check', ['input' => 'email', 'user_info' => $user_info])
            @include('components.register_user_form_check', ['input' => 'password', 'user_info' => $user_info])

            <div class="mod_register_user_info">
                登録情報を修正する場合は<a href="register_user_form" class="link_primary">こちら</a>
            </div>
        
            <div class="submit_btn">
                <input type = "submit" class="btn_primary_more_than_4_chars" value="この内容で登録する">
            </div>
        </form>
    </div>
</div>
@endsection
