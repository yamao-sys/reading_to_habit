@extends('layouts.after_login')

@section('title', 'Readingtohabit-お問い合わせ内容の情報入力')

@section('content')
<div id="contact">
    <div v-if="smaller === true" class="wrapper">
        <div class="content">
            <div class="content_title">お問い合わせフォーム</div>
            @include('components.contact_form')
        </div>
        @include('components.footer_nav', ['current_page' => 'contact'])
    </div>

    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'contact'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="p_after_login_large">
                        <div class="content_title">お問い合わせフォーム</div>
                        @include('components.contact_form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
