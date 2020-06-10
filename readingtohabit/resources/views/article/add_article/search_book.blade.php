@extends('layouts.after_login')

@section('title', 'Readingtohabit-読書記録対象の本を探す')

@section('content')
<div id="content">
    <div class="add_article_search_book_header">
        <h3>記録を書く対象の本を探す</h3>
    </div>
    <div class="add_article_search_book_search_area">
        <div class="search_area">
            <input v-model="word" type="text" class="search_book" placeholder="著書名の一部または全てから検索">
            <button v-on:click="search_books(word)" class="btn_primary_search_btn">検索する</button>
        </div>
    </div>

    <div v-if="search_results.length > 0" class="search_results">
        <div v-for="book in search_results" class="search_result_area">
            <a v-if="'imageLinks' in book.volumeInfo" v-bind:href="'add_article_form?bookimg=' + book.volumeInfo.imageLinks.smallThumbnail.replace(/&/g, '-') + '&bookname=' + book.volumeInfo.title + '&author=' + book.volumeInfo.authors" v-on:click="reset_word(word)" class="link_dark">
                <div class="search_result_content">
                    <div class="thumbnail">
                        <img v-bind:src="book.volumeInfo.imageLinks.smallThumbnail" class="thumbnail_size">
                    </div>
                    <div class="book_info">
                        <div class="bookname">(%book.volumeInfo.title%)</div>
                        <div><span v-if="'authors' in book.volumeInfo">(%book.volumeInfo.authors[0]%)</span></div>
                    </div>
                </div>
            </a>
            <a v-else v-bind:href="'add_article_form?bookimg=\ImgPathConst::NOIMG_PATH&bookname=' + book.volumeInfo.title + '&author=' + book.volumeInfo.authors" v-on:click="reset_word(word)" class="link_dark">
                <div class="search_result_content">
                    <div class="thumbnail">
                        <img src="{{\ImgPathConst::NOIMG_PATH}}" class="thumbnail_size">
                    </div>
                    <div class="book_info">
                        <div class="bookname">(%book.volumeInfo.title%)</div>
                        <div><span v-if="'authors' in book.volumeInfo">(%book.volumeInfo.authors[0]%)</span></div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
