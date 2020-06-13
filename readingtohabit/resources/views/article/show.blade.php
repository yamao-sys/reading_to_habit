@extends('layouts.after_login')

@section('title', 'Readingtohabit-読書記録の確認')

@section('content')
<div id="article_id">{{$book_info['id']}}</div>
<div id="favorite_flag">{{$book_info['favorite']}}</div>

<div id="show_article">
    <div v-if="delete_modal" class="modal_area">
        <div v-if="delete_modal_form" class="delete_modal">
            <div class="close_btn_area">
                <div class="close_btn" v-on:click="close_delete_modal()"><i class="fas fa-times close_white"></i></div>
            </div>
            <div class="p_2rem">
                <div class="confirm_delete">
                    本当に削除しますか?
                </div>
                <div class="btn_vertical_align_horizon_between">
                    <button class="btn_danger_less_than_4_chars mr_3" v-on:click="delete_article_do()">削除する</button>
                    <button class="btn_default_less_than_4_chars" v-on:click="close_delete_modal()">閉じる</button>
                </div>
            </div>
        </div>
        <div v-if="delete_modal_finish" class="delete_modal">
            <div class="close_btn_area">
                <div class="close_btn" v-on:click="redirect_to_index()"><i class="fas fa-times close_white"></i></div>
            </div>
            <div class="p_2rem">
                <div class="confirm_delete">
                    削除しました。
                </div>
                <div class="btn_vertical_align_horizon_between">
                    <button class="btn_primary_less_than_4_chars" v-on:click="redirect_to_index()">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="show_article_bookimg_area">
            <div class="show_article_bookimg_content">
                <img class="show_article_bookimg" src="{{$book_info['bookimg']}}">
            </div>
        </div>
        <div class="show_article_book_detail">
            <div class="bookname">
                {{$book_info['bookname']}}
            </div>
            <div class="book_detail">
                <span class="mr_3_inline">{{$book_info['author']}}</span>
            </div>
        </div>
        <div class="show_article_favorite">
            <div v-if="not_favorite === true" v-on:click="add_favorite()" class="not_favorite_icon">
                <i class="fas fa-star star_1point5x star_default"></i>
            </div>
            <div v-if="favorite === true" v-on:click="delete_favorite()" class="favorite_icon">
                <i class="fas fa-star star_1point5x star_yellow"></i>
            </div>
        </div>
        <div class="show_element">
            <label>学んだこと</label>
            <div class="form_textarea">{{$book_info['learning']}}</div>
        </div>
        <div class="show_element">
            <label>学びをどのように行動に活かすか</label>
            <div class="form_textarea">{{$book_info['action']}}</div>
        </div>
        <div class="show_element">
            <label>リマインドメールの配信</label>
            <div>{{$book_info['mail_flag']}}</div>
        </div>
        @isset ($book_info['next_mail_date'])
        <div class="show_element">
            <label>次回のリマインドメールの配信日</label>
            <div>{{$book_info['next_mail_date']}}</div>
        </div>
        @endisset
        <div class="btn_vertical_align">
            <button class="btn_primary_less_than_4_chars mr_3"><a href="edit_article_form/{{$book_info['id']}}" class="link_white">編集する</a></button>
            <button class="btn_danger_less_than_4_chars" v-on:click="open_delete_modal()">削除する</button>
        </div>
    </div>
</div>
@endsection
