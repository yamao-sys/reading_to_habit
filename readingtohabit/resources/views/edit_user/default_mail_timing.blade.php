@extends('layouts.after_login')

@section('title', 'Readingtohabit-デフォルト配信タイミング編集の情報入力')

<div id="edit_default_mail_timing">
    @isset($dialog)
    <div id="dialog">{{$dialog}}</div>
    <div v-if="dialog === true" class="show_dialog">{{$dialog}}</div>
    @endisset
    <div v-if="smaller === true" class="wrapper" v-on:mousemove="close_dialog()" v-on:scroll="close_dialog()">
        <div class="content">
            @include('components.edit_user_head', ['edit_target' => 'default_mail_timing'])

            <form action="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_default_mail_timing" method="post">
                {{ csrf_field() }}
                <div class="mb_2rem">
                    リマインドメールのデフォルト配信タイミングの設定です。<br>
                    この設定が、記録の新規作成時にデフォルトで表示されます。
                </div>
                <div class="form_element">
                    <label>配信タイミング選択</label>
                    @include('components.edit_default_mail_timing_select_input', ['default_mail_timing_select' => $default_mail_timing_select])
                </div>
                <div class="form_element">
                    <label>配信タイミング</label>
                    @include('components.edit_default_mail_timing_input', ['default_mail_timing' => $default_mail_timing])
                </div>
                <input type="submit" class="btn_primary_less_than_4_chars" value="更新する">
            </form>
        </div>

        @include('components.footer_nav', ['current_page' => 'edit_password'])

    </div>

    <div v-if="larger === true" v-on:mousemove="close_dialog()" v-on:scroll="close_dialog()">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'edit_default_mail_timing'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="edit_user_form_area">
                        <form action="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_default_mail_timing" method="post">
                            {{ csrf_field() }}
                            <div class="mb_2rem">
                                リマインドメールのデフォルト配信タイミングの設定です。<br>
                                この設定が、記録の新規作成時にデフォルトで表示されます。
                            </div>
                            <div class="form_element">
                                <label>配信タイミング選択</label>
                                @include('components.edit_default_mail_timing_select_input', ['default_mail_timing_select' => $default_mail_timing_select])
                            </div>
                            <div class="form_element">
                                <label>配信タイミング</label>
                                @include('components.edit_default_mail_timing_input', ['default_mail_timing' => $default_mail_timing])
                            </div>
                            <input type="submit" class="btn_primary_less_than_4_chars" value="更新する">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
