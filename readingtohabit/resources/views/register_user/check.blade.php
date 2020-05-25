@extends('layouts.default')

@section('title', 'Readingtohabitユーザー登録情報確認')

@section('content')
<div class="register_user_content">
    <div class="register_user_form_top">
        <div class="register_user_phase_area">
            <div class="register_user_phase_content">
                <div class="phase">登録情報入力</div>
                <div class="register_user_default"></div>
            </div>
            <div class="register_user_phase_content">
                <div class="phase">登録情報確認</div>
                <div class="register_user_in_phase"></div>
            </div>
            <div class="register_user_phase_content">
                <div class="phase">登録完了</div>
                <div class="register_user_default"></div>
            </div>
        </div>
    </div>

    <div class="register_user_check_area">
        <form action="register_user_do" method="post">
        {{ csrf_field() }}
            @include('components.register_user_form_check', ['input' => 'name', 'register_user_info' => $register_user_info])
            @include('components.register_user_form_check', ['input' => 'email', 'register_user_info' => $register_user_info])
            @include('components.register_user_form_check', ['input' => 'password', 'register_user_info' => $register_user_info])

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
