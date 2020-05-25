@extends('layouts.default')

@section('title', 'パスワードリセット用メールの情報入力')

@section('content')
<div class="content">
    <div class="content_title">パスワードリセット用メールの送信</div>
    <div class="mb_2rem">
        パスワードリセット用メールを送信いたします。<br>
        下記のフォームにご登録のメールアドレスをご入力のうえ、「送信する」をクリックしてください。
    </div>
    <form action="reset_password_mail_do" method="post">
        {{ csrf_field() }}
        <div class="form_element">
            <label>メールアドレス</label>
            <input type="email" name="email" class="form_input">
            @isset($errors)
                @if ($errors->has('email'))
                <div class="error_msg">
                    <p>{{ $errors->first('email')}}</p>
                </div>
                @endif
            @endisset
        </div>
        <input type="submit" class="btn_primary_less_than_4_chars" value="送信する">
    </form>
</div>
@endsection
