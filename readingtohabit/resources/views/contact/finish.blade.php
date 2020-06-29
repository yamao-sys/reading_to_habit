@if (empty(session()->get('user_id')))
@extends('layouts.default')

@section('title', 'Readingtohabit-お問い合わせ送信完了')

@section('content')
<div class="content">
    <div class="content_title">お問い合わせ完了</div>
    <div class="mb_2rem">
        お問い合わせいただき、ありがとうございます。<br>
        対応の状況をご入力いただいたメールアドレス宛てに送信いたします。<br>
        よろしくお願いいたします。<br>
        &lt;&lt;&emsp;<a href="/" class="link_primary">トップへ</a>
    </div>
</div>
@endsection
@else
@extends('layouts.after_login')

@section('title', 'Readingtohabit-お問い合わせ内容の情報確認')
<div id="contact">
    <div v-if="smaller === true" class="wrapper">
        <div class="content">
            <div class="content_title">お問い合わせ完了</div>
            <div class="mb_2rem">
            お問い合わせいただき、ありがとうございます。<br>
            対応の状況をご入力いただいたメールアドレス宛てに送信いたします。<br>
            よろしくお願いいたします。<br>
        </div>
        @include('components.footer_nav', ['current_page' => 'contact'])
    </div>

    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'contact'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="contact_form_area">
                        <div class="content">
                            <div class="content_title">お問い合わせ完了</div>
                                <div class="mb_2rem">
                                お問い合わせいただき、ありがとうございます。<br>
                                対応の状況をご入力いただいたメールアドレス宛てに送信いたします。<br>
                                よろしくお願いいたします。<br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
