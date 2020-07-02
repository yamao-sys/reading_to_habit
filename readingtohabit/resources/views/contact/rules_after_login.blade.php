@extends('layouts.after_login')

@section('title', 'Readingtohabit-利用契約')

<div id="rules">
    <div v-if="smaller === true" class="wrapper">
        <div class="content">
        @include('components.rules')
        </div>
        
        @include('components.footer_nav', ['current_page' => 'rules'])

    </div>
    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'rules'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="p_after_login_large">
                    @include('components.rules')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
