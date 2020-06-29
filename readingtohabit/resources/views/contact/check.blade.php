@if (empty(session()->get('user_id')))
@extends('layouts.default')

@section('title', 'Readingtohabit-お問い合わせ内容の情報確認')

@section('content')
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
        <div class="form_textarea">{{session()->get('contact_info_contact')}}</div>
    </div>
    <form action="contact_do" method="post">
        {{ csrf_field() }}
        <div class="btn_vertical_align">
            <input type="submit" class="btn_primary_more_than_4_chars" value="送信する">
            <button class="btn_default_more_than_4_chars"><a href="contact_check" class="link_white">内容を修正する</a></button>
        </div>
    </form>
</div>
@endsection
@else
@extends('layouts.after_login')

@section('title', 'Readingtohabit-お問い合わせ内容の情報確認')
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
                <div class="form_textarea">{{session()->get('contact_info_contact')}}</div>
            </div>
            <form action="contact_do" method="post">
                {{ csrf_field() }}
                <div class="btn_vertical_align">
                    <input type="submit" class="btn_primary_more_than_4_chars" value="送信する">
                    <button class="btn_default_more_than_4_chars"><a href="contact_check" class="link_white">内容を修正する</a></button>
                </div>
            </form>
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
                            <div class="form_textarea">{{session()->get('contact_info_contact')}}</div>
                        </div>
                        <form action="contact_do" method="post">
                            {{ csrf_field() }}
                            <div class="btn_vertical_align">
                                <input type="submit" class="btn_primary_more_than_4_chars" value="送信する">
                                <button class="btn_default_more_than_4_chars"><a href="contact_check" class="link_white">内容を修正する</a></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
