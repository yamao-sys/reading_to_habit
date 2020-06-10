@extends('layouts.after_login')

@section('title', 'Readingtohabit-読書記録一覧')

<div id="articles">
    <div v-if="smaller === true" class="wrapper">
        <div class="content">
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
                        <div class="list_article_author">{{$article->author}}</div>
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
        
        @include('components.footer_nav', ['current_page' => 'search_articles'])

    </div>
    <div v-if="larger === true">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'search_articles'])

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
                                <div class="list_article_author">{{$article->author}}</div>
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
