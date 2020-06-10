@extends('layouts.default')

@section('title', 'Readingtohabitユーザー登録完了確認')

@section('content')
<div class="content">
    <div class="content_title">ユーザー登録完了</div>
    <div>
        <div class="mb_3">
            Readingtohabitにユーザー登録をしていただき、誠にありがとうございます。<br>
            ご登録いただいたメールアドレス宛てに「ユーザー登録完了」のメールを送信しました。
        </div>
        <div class="mb_3">
            ログインは<a href="login" class="link_primary">こちら</a>から
        </div>
        <div class="mb_3">
            ※もし、メールが届いていない場合は<a href="resend_mail_form" class="link_primary">こちら</a>から
        </div>
    </div>
</div>
@endsection
