@extends('layouts.default')

@section('title', 'ユーザー登録完了メールの再送信完了')

@section('content')
<div class="content">
    <div class="content_title">ユーザー登録完了メールの送信完了</div>
    <div class="mb_2rem">
        ご入力いただいたメールアドレス宛てに「ユーザー登録完了」のメールを送信しました。
    </div>
    <div class="mb_2rem">
        ログインは<a href="login" class="link_primary">こちら</a>から
    </div>
    <div>
        ※もし、メールが届いていない場合は<a href="contact_form" class="link_primary">こちら</a>からお問い合わせください。
    </div>
</div>
@endsection
