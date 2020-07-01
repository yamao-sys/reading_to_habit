@extends('layouts.after_login')

@section('title', 'Readingtohabit-プライバシーポリシー')

<div id="privacy_policy">
    <div v-if="smaller === true" class="wrapper">
        <div class="content">
        @include('components.privacy_policy')
        </div>
        
        @include('components.footer_nav', ['current_page' => 'privacy_policy'])

    </div>
    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'privacy_policy'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="p_after_login_large">
                    @include('components.privacy_policy')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
