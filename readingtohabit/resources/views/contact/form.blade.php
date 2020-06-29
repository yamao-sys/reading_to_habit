@extends('layouts.default')

@section('title', 'Readingtohabit-お問い合わせ内容の情報入力')

@section('content')
<div class="content">
    <div class="content_title">お問い合わせ内容の情報入力</div>
    <div class="mb_2rem">
        お問い合わせ内容の情報入力画面です。<br>
        下記のフォームをご入力のうえ、「内容確認画面へ」をクリックしてください。
    </div>
    <form action="contact_check" method="post">
        {{ csrf_field() }}
        <div class="form_element">
            <label>メールアドレス</label>
            @if (empty(session()->get('contact_info_email')))
            <input type="email" name="email" class="form_input" value="{{old('email')}}">
            @else
            <input type="email" name="email" class="form_input" value="{{session()->get('contact_info_email')}}">
            @endif
            @isset($errors)
                @if ($errors->has('email'))
                <div class="error_msg">
                    <p>{{ $errors->first('email')}}</p>
                </div>
                @endif
            @endisset
        </div>
        <div class="form_element">
            <label>お問い合わせ内容</label>
            @if (empty(session()->get('contact_info_contact')))
            <textarea name="contact" class="form_textarea">{{old('contact')}}</textarea>
            @else
            <textarea name="contact" class="form_textarea">{{session()->get('contact_info_contact')}}</textarea>
            @endif
            @isset($errors)
                @if ($errors->has('contact'))
                <div class="error_msg">
                    <p>{{ $errors->first('contact')}}</p>
                </div>
                @endif
            @endisset
        </div>
        <input type="submit" class="btn_primary_more_than_4_chars" value="内容確認画面へ">
    </form>
</div>
@endsection
