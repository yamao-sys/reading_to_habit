@extends('layouts.after_login')

@section('title', 'Readingtohabit-お問い合わせ内容の情報確認')

@section('content')
<div id="contact">
    <div v-if="smaller === true" class="wrapper">
        <div class="content">
            <div class="content_title">お問い合わせ完了</div>
            <div class="mb_2rem">
            お問い合わせいただき、ありがとうございます。<br>
            対応の状況をご入力いただいたメールアドレス宛てに送信いたします。<br>
            よろしくお願いいたします。
            </div>
        </div>
        @include('components.footer_nav', ['current_page' => 'contact'])
    </div>

    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'contact'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="p_after_login_large">
                        <div class="content_title">お問い合わせ完了</div>
                        <div class="mb_2rem">
                        お問い合わせいただき、ありがとうございます。<br>
                        対応の状況をご入力いただいたメールアドレス宛てに送信いたします。<br>
                        よろしくお願いいたします。
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
