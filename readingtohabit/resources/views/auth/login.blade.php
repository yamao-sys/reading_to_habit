@extends('layouts.default')

@section('title', 'Readingtohabitログイン情報入力')

@section('content')
<div class="login_content">
    <div class="login_form">
        <form action="login" method="post">
            {{ csrf_field() }}
            @if($errors->has('is_not_exist'))
            <div class="error_msg">
                <p>{{ $errors->first('is_not_exist') }}</p>
            </div>
            @endif

            <div class="form_element">
                <label>メールアドレス</label>
                <input type="email" class="form_input" name="email" value="{{old('email')}}">
                @if ($errors->has('email'))
                <div class="error_msg">
                    <p>{{ $errors->first('email') }}</p>
                </div>
                @endif
            </div>
            
            <div class="form_element">
                <label>パスワード</label>
                <input type="password" class="form_input" name="password" value="{{old('password')}}">
                @if ($errors->has('password'))
                <div class="error_msg">
                    <p>{{ $errors->first('password') }}</p>
                </div>
                @endif
            </div>

            <div class="mb_2rem">
                <input type="checkbox" name="auto_login" value="1" {{ old('auto_login') === "1" ? 'checked="checked"': ''}}>自動ログインを許可する
            </div>
        
            <div class="submit_btn">
                <input type = "submit" class="btn_primary_less_than_4_chars" value="ログイン">
            </div>

            <div class="to_reset_password">
                パスワードを忘れた場合は<a href="reset_password_mail_form" class="link_primary">こちら</a>からパスワードの再発行をお願いいたします。
            </div>
        </form>
    </div>
</div>
@endsection
