@extends('layouts.default')

@section('title', 'パスワードリセット情報入力')

@section('content')
<div class="content">
    <h2 class="title">パスワードリセット</h2>
    <div class="mb_2rem">
        パスワードリセットを行います。<br>
        下記のフォームをご入力のうえ、「更新する」をクリックしてください。
    </div>
    <form action="reset_password_do?key={{ $token }}" method="post">
        {{ csrf_field() }}
        <div class="reset_password_form_input_password">
            <label>新しいパスワード</label>
            <input type="password" name="password" class="reset_password_input">
            @isset($errors)
                @if ($errors->has('password'))
                <div class="error_msg">
                    <p>{{ $errors->first('password')}}</p>
                </div>
                @endif
            @endisset
        </div>
        <div class="reset_password_form_input_password_to_check">
            <label>パスワード確認用</label>
            <input type="password" name="password_to_check" class="reset_password_input">
            @isset($errors)
                @if ($errors->has('password_to_check'))
                <div class="error_msg">
                    <p>{{ $errors->first('password_to_check')}}</p>
                </div>
                @endif
            @endisset
        </div>
        <input type="submit" class="btn_primary_less_than_4_chars" value="更新する">
    </form>
</div>
@endsection
