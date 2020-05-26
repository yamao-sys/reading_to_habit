@extends('layouts.after_login')

@section('title', 'Readingtohabit-読書記録一覧')

<div id="articles">
    <div v-on:click="close_menu()" v-bind:class="{menu_area:open_menu_flag, menu_area_hidden:close_menu_flag}">
        <div v-bind:class="{menu_content:menu_content_flag, menu_content_hidden:menu_content_hidden_flag}">
            <div class="mt_2rem mb_2rem">
                <a class="menu_list" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_profile">プロフィール編集</a>
            </div>
            <div class="mb_2rem">
                <a class="menu_list" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_password">パスワード編集</a>
            </div>
            <div class="mb_2rem">
                <a class="menu_list" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_default_mail_timing">メールタイミング編集</a>
            </div>
            <div class="mb_2rem">
                <a class="menu_list" href="{{\DocumentRootConst::DOCUMENT_ROOT}}rules">利用規約</a>
            </div>
            <div class="mb_2rem">
                <a class="menu_list" href="{{\DocumentRootConst::DOCUMENT_ROOT}}privacy_policy">プライバシーポリシー</a>
            </div>
            <div class="mb_2rem">
                <a class="menu_list" href="{{\DocumentRootConst::DOCUMENT_ROOT}}logout">ログアウト</a>
            </div>
        </div>
    </div>

    <div v-if="smaller === true" class="wrapper">
        <div class="content">
            <div id="top_bar">
                <div class="menu_profile_img_content">
                    <img class="menu_profile_img" src="{{asset(session()->get('profile_img'))}}">
                </div>

                <div class="top_bar_content">
                    <div v-on:click="open_menu()" class="open_menu"><i class="fas fa-ellipsis-h"></i></div>
                    <a class="to_add_article" href="{{\DocumentRootConst::DOCUMENT_ROOT}}add_article_search_book">
                        <i class="fas fa-pen-square mr_2_inline"></i>記録を書く
                    </a>
                </div>
            </div>
            <div class="num_of_articles">
                <span class="inline_font_bold">{{$num_of_articles}}</span>件の記録
            </div>
            @foreach ($articles as $article)
            <div class="list_article_area">
                <a href="{{\DocumentRootConst::DOCUMENT_ROOT}}show_article/{{$article->id}}" class="list_article_link">
                    <div class="list_article_bookimg">
                        <img src="{{asset($article->bookimg)}}" class="list_article_thumbnail">
                    </div>
                    <div class="list_article_detail">
                        <div class="list_article_bookname">{{$article->bookname}}</div>
                        <div class="mb_3">{{$article->author}}</div>
                        @if (!empty($article->article_mail_timing->next_send_date))
                        <div>
                            <span class="list_article_date"><i class="far fa-clock times_icon"></i>次回リマインド日：{{$article->article_mail_timing->next_send_date}}</span>
                        </div>
                        @endif
                    </div>
                </a>
                <div class="list_article_favorite" data="{{$article->id}}">
                    <div class="list_article_id" data="{{$article->id}}"></div>
                    <div class="list_article_favorite_flag" data="{{$article->favorite}}"></div>
                    <div v-if="not_favorite[{{$article->id}}] === true" v-on:click="add_favorite()" class="list_article_not_favorite_icon" data="{{$article->id}}">
                        <i class="fas fa-star star_1point5x star_default" data="{{$article->id}}"></i>
                    </div>
                    <div v-if="favorite[{{$article->id}}] === true" v-on:click="delete_favorite()" class="list_article_favorite_icon" data="{{$article->id}}">
                        <i class="fas fa-star star_1point5x star_yellow" data="{{$article->id}}"></i>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="pagination_area">
                {{$articles->links()}}
            </div>
        </div>
        
        @include('components.footer_nav', ['current_page' => 'articles'])

    </div>
    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'articles'])

            <div class="main_content_area">
                <div class="main_content">
                    <div class="num_of_articles">
                        <span class="inline_font_bold">{{$num_of_articles}}</span>件の記録
                    </div>

                    @foreach ($articles as $article)
                    <div class="list_article_area">
                        <a href="{{\DocumentRootConst::DOCUMENT_ROOT}}show_article/{{$article->id}}" class="list_article_link">
                            <div class="list_article_bookimg">
                                <img src="{{asset($article->bookimg)}}" class="list_article_thumbnail">
                            </div>
                            <div class="list_article_detail">
                                <div class="list_article_bookname">{{$article->bookname}}</div>
                                <div class="mb_3">{{$article->author}}</div>
                                @if (!empty($article->article_mail_timing->next_send_date))
                                <div>
                                    <span class="list_article_date"><i class="far fa-clock times_icon"></i>次回リマインド日：{{$article->article_mail_timing->next_send_date}}</span>
                                </div>
                                @endif
                            </div>
                        </a>
                        <div class="list_article_favorite" data="{{$article->id}}">
                            <div class="list_article_id" data="{{$article->id}}"></div>
                            <div class="list_article_favorite_flag" data="{{$article->favorite}}"></div>
                            <div v-if="not_favorite[{{$article->id}}] === true" v-on:click="add_favorite()" class="list_article_not_favorite_icon" data="{{$article->id}}">
                                <i class="fas fa-star star_1point5x star_default" data="{{$article->id}}"></i>
                            </div>
                            <div v-if="favorite[{{$article->id}}] === true" v-on:click="delete_favorite()" class="list_article_favorite_icon" data="{{$article->id}}">
                                <i class="fas fa-star star_1point5x star_yellow" data="{{$article->id}}"></i>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="pagination_area">
                        {{$articles->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
