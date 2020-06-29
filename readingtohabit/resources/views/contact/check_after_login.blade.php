@extends('layouts.after_login')

@section('title', 'Readingtohabit-お問い合わせ内容の情報確認')

@section('content')
<div id="contact">
    <div v-if="smaller === true" class="wrapper">
        <div class="content">
            <div class="content_title">お問い合わせ内容の情報確認</div>
            <div class="mb_2rem">
                お問い合わせ内容の情報確認画面です。<br>
                内容をご確認のうえ、「送信する」をクリックしてください。
            </div>
            <div class="show_element">
                <label>メールアドレス</label>
                <div>{{session()->get('contact_info_email')}}</div>
            </div>
            <div class="show_element">
                <label>お問い合わせ内容</label>
                <textarea class="form_textarea" readonly>{{session()->get('contact_info_contact')}}</textarea>
            </div>
            <div class="btn_vertical_align">
                <button class="btn_primary_more_than_4_chars mr_3"><a href="contact_do" class="link_white">送信する</a></button>
                <button class="btn_default_more_than_4_chars"><a href="contact_form" class="link_dark">内容を修正する</a></button>
            </div>
        </div>
        @include('components.footer_nav', ['current_page' => 'contact'])
    </div>

    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'contact'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="contact_form_area">
                        <div class="content_title">お問い合わせ内容の情報確認</div>
                            <div class="mb_2rem">
                            お問い合わせ内容の情報確認画面です。<br>
                            内容をご確認のうえ、「送信する」をクリックしてください。
                        </div>
                        <div class="show_element">
                            <label>メールアドレス</label>
                            <div>{{session()->get('contact_info_email')}}</div>
                        </div>
                        <div class="show_element">
                            <label>お問い合わせ内容</label>
                            <textarea class="form_textarea" readonly>{{session()->get('contact_info_contact')}}</textarea>
                        </div>
                        <div class="btn_vertical_align">
                            <button class="btn_primary_more_than_4_chars mr_3"><a href="contact_do" class="link_white">送信する</a></button>
                            <button class="btn_default_more_than_4_chars"><a href="contact_form" class="link_dark">内容を修正する</a></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
