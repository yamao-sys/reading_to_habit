@extends('layouts.default')

@section('title', 'ユーザー登録完了メールの再送信情報入力')

@section('content')
<div class="content">
    <div class="content_title">ユーザー登録完了メールの再送信</div>
    <div class="mb_2rem">
        <div>
            ユーザー登録完了のメールが届かない場合にはメールの受信設定をご確認ください。
        </div>
        <ul>
            <li>迷惑メールフォルダに入っていないか</li>
            <li>迷惑メール対策の設定でURLを含むメールを拒否していないかどうか</li>
            <li>ドメイン指定受信をされている場合には@readingtohabit.co.jpを登録してください。</li>
        </ul>
        <div>
            すべてご確認いただいたあとに下記のフォームにご登録のメールアドレスを入力し、<br>
            「送信する」をクリックしていただけますようお願いいたします。
        </div>
        <br>
        <div>
            もし、登録時に入力されたメールアドレスが間違っていてメールが届かない場合には、<br>
            こちらで修正する必要がございますので、下記の情報を明記の上、<a href="contact_form" class="link_primary">こちら</a>からお問い合わせください。
        </div>
        <ul>
            <li>誤って入力したメールアドレス(もしおわかりになる場合)</li>
            <li>正しいメールアドレス</li>
        </ul>
    </div>
    <form action="resend_mail_do" method="post">
        {{ csrf_field() }}
        <div class="form_element">
            <label>メールアドレス</label>
            <input type="email" class="form_input" name="email" value="{{old('email')}}">
            @empty ($errors)
            @else
            @if ($errors->has('email'))
            <div class="error_msg">
                <p>{{ $errors->first('email') }}</p>
            </div>
            @endif
            @endempty
        </div>
        <input type = "submit" class="btn_primary_less_than_4_chars" value="送信する">
    </form>
</div>
@endsection
