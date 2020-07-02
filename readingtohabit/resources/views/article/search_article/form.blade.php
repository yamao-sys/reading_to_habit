@extends('layouts.after_login')

@section('title', 'Readingtohabit-読書記録検索の情報入力')

@section('content')

<div id="search_articles">
    <div v-if="smaller">
        <div class="content">
            <form action="search_results" method="post">
                <div class="content_title">記録検索フォーム</div>
                {{ csrf_field() }}
                <div class="form_element">
                    <label>著書名または著者名</label>
                    <input type="text" name="bookinfo" class="search_article_input">
                </div>
                <div class="form_element">
                    <label>最終更新日</label>
                    <div class="form_dropdown">
                        <select name="last_update">
                            <option value="未選択" selected>未選択</option>
                            @php
                            for ($i=1; $i <= \SearchArticleConst::LAST_UPDATED; $i++) {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            @endphp
                        </select>ヶ月以内
                    </div>
                </div>
                <div class="form_element">
                    <label>リマインドメール</label>
                    <div class="checkbox_align">
                        <div class="mr_3"><input type="checkbox" name="mail[]" value="1">配信する</div>
                        <div><input type="checkbox" name="mail[]" value="0">配信しない</div>
                    </div>
                </div>
                <div class="btn_vertical_align">
                    <input type="submit" class="btn_primary_less_than_4_chars mr_5" value="検索する">
                    <button class="btn_default_less_than_4_chars" onclick="javascript:window.history.back(-1);return false;">戻る</button>
                </div>
            </form>
        </div>

        @include('components.footer_nav', ['current_page' => 'search_articles'])
    </div>

    <div v-if="larger">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'search_articles'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="p_after_login_large">
                        <div class="content_title">記録検索フォーム</div>
                        <form action="search_results" method="post">
                            {{ csrf_field() }}
                            <div class="form_element">
                                <label>著書名または著者名</label>
                                <input type="text" name="bookinfo" class="search_article_input">
                            </div>
                            <div class="form_element">
                                <label>最終更新日</label>
                                <div class="form_dropdown">
                                    <select name="last_update">
                                        <option value="未選択" selected>未選択</option>
                                        @php
                                        for ($i=1; $i <= \SearchArticleConst::LAST_UPDATED; $i++) {
                                            echo '<option value="'.$i.'">'.$i.'</option>';
                                        }
                                        @endphp
                                    </select>ヶ月以内
                                </div>
                            </div>
                            <div class="form_element">
                                <label>リマインドメール</label>
                                <div class="checkbox_align">
                                    <div class="mr_3"><input type="checkbox" name="mail[]" value="1">配信する</div>
                                    <div><input type="checkbox" name="mail[]" value="0">配信しない</div>
                                </div>
                            </div>
                            <div class="btn_vertical_align">
                                <input type="submit" class="btn_primary_less_than_4_chars mr_5" value="検索する">
                                <button class="btn_default_less_than_4_chars" onclick="javascript:window.history.back(-1);return false;">戻る</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
