@extends('layouts.after_login')

@section('title', 'Readingtohabit-読書記録お気に入り一覧')

<div id="favorites">
    <div v-if="smaller" class="wrapper">
        <div id="init_num_of_favorites">{{$num_of_favorites}}</div>
        @if ($num_of_favorites === 0)
        <div class="content">
            <div>
                気に入った記録は<i class="fas fa-star icon_color_yellow"></i>をクリックし、お気に入りに登録しましょう!
            </div>
        </div>
        @else
        <div class="content">
            <div class="num_of_favorites">
                <span class="inline_font_bold">{{$num_of_favorites}}</span>件の記録
            </div>

            @foreach ($favorites as $article)
            <div class="list_article_area">
                <a href="show_article/{{$article->id}}" class="list_article_link">
                    <div class="list_article_bookimg">
                        <img src="{{$article->bookimg}}" class="list_article_thumbnail object-fit-img">
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
                    <div v-on:click="delete_favorite()" class="list_article_favorite_icon" data="{{$article->id}}">
                        <i class="fas fa-star star_1point5x star_yellow" data="{{$article->id}}"></i>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="pagination_area">
                {{$favorites->links()}}
            </div>
        </div>
        @endif

        @include('components.footer_nav', ['current_page' => 'favorites'])
    </div>
    
    <div v-if="larger">
        <div id="larger_wrapper">
            @include('components.side_menu_bar', ['current_page' => 'favorites'])

            <div class="main_content_area">
                @if ($num_of_favorites === 0)
                <div class="main_content">
                    <div class="message_about_favorite">
                        <div class="text_with_icon">気に入った記録は</div>
                        <i class="fas fa-star icon_color_yellow_with_text"></i>
                        <div class="text_with_icon">をクリックし、お気に入りに登録しましょう!</div>
                    </div>
                </div>
                @else
                <div class="main_content">
                    <div class="num_of_favorites">
                        <span class="inline_font_bold">{{$num_of_favorites}}</span>件の記録
                    </div>

                    @foreach ($favorites as $article)
                    <div class="list_article_area">
                        <a href="show_article/{{$article->id}}" class="list_article_link">
                            <div class="list_article_bookimg">
                                <img src="{{$article->bookimg}}" class="list_article_thumbnail object-fit-img">
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
                            <div v-on:click="delete_favorite()" class="list_article_favorite_icon" data="{{$article->id}}">
                                <i class="fas fa-star star_1point5x star_yellow" data="{{$article->id}}"></i>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div class="pagination_area">
                        {{$favorites->links()}}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
