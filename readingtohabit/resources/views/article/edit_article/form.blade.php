@extends('layouts.after_login')

@section('title', 'Readingtohabit-読書記録の投稿情報入力')

@section('content')
<div class="content" id="edit_article_form">
    <form action="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_article_do/{{$article_info['id']}}" method="post">
        {{ csrf_field() }}
        <div class="edit_article_bookimg_area">
            <div class="edit_article_bookimg_content">
                <img class="edit_article_bookimg" src="{{$article_info['bookimg']}}">
            </div>
        </div>
        <div class="edit_article_book_detail">
            <div class="bookname">
                {{$article_info['bookname']}}
            </div>
            <div class="book_detail">
                <span class="mr_3_inline">{{$article_info['author']}}</span>
            </div>
        </div>
        <div class="form_element">
            <label>学んだこと</label>
            <textarea name="learning" class="form_textarea">{{old('learning') !== null ? old('learning'): $article_info['learning']}}</textarea>
            @isset($errors)
                @if ($errors->has('learning'))
                <div class="error_msg">
                    <p>{{ $errors->first('learning')}}</p>
                </div>
                @endif
            @endisset
        </div>
        <div class="form_element">
            <label>学びをどのように行動に活かすか</label>
            <textarea name="action" class="form_textarea">{{old('action') !== null ? old('action'): $article_info['action']}}</textarea>
            @isset($errors)
                @if ($errors->has('action'))
                <div class="error_msg">
                    <p>{{ $errors->first('action')}}</p>
                </div>
                @endif
            @endisset
        </div>
        @if (old('mail_flag') === '1')
        <div id="edit_article_mail_flag">1</div>
        @elseif (old('mail_flag') === '0')
        <div id="edit_article_mail_flag">0</div>
        @else
        <div id="edit_article_mail_flag">{{$article_info['mail']}}</div>
        @endif
        <div class="edit_article_mail_flag">
            <label>リマインドメールの配信</label>
            <div class="radiobtn_align">
                @if ($article_info['mail'] === 1)
                <input v-model="mail_flag" type="radio" name="mail_flag" value="1" checked>配信する
                <input v-model="mail_flag" type="radio" name="mail_flag" value="0">配信しない
                @elseif ($article_info['mail'] === 0)
                <input v-model="mail_flag" type="radio" name="mail_flag" value="1">配信する
                <input v-model="mail_flag" type="radio" name="mail_flag" value="0" checked>配信しない
                @else
                <input v-model="mail_flag" type="radio" name="mail_flag" value="1" checked>配信する
                <input v-model="mail_flag" type="radio" name="mail_flag" value="0">配信しない
                @endif
            </div>
        </div>
        @empty (old('mail_timing_select'))
            @if ($mail_info['mail_timing_select'] === 'by_day')
            <div id="edit_article_mail_timing_select">by_day</div>
            @elseif ($mail_info['mail_timing_select'] === 'by_week')
            <div id="edit_article_mail_timing_select">by_week</div>
            @elseif ($mail_info['mail_timing_select'] === 'by_month')
            <div id="edit_article_mail_timing_select">by_month</div>
            @else
            <div id="edit_article_mail_timing_select">by_day</div>
            @endif
        @else
            @if (old('mail_timing_select') === 'by_day')
            <div id="edit_article_mail_timing_select">by_day</div>
            @elseif (old('mail_timing_select') === 'by_week')
            <div id="edit_article_mail_timing_select">by_week</div>
            @elseif (old('mail_timing_select') === 'by_month')
            <div id="edit_article_mail_timing_select">by_month</div>
            @else
            <div id="edit_article_mail_timing_select">by_day</div>
            @endif
        @endempty
        <div v-bind:class="{edit_article_mail_timing_select:mail_on, edit_article_mail_timing_select_none:mail_off}">
            <label>リマインドメールの配信タイミング</label>
            <div class="mb_3">
                <input v-model="mail_timing_select" type="radio" name="mail_timing_select" value="by_day">日毎
            </div>
            <div class="mb_3">
                <input v-model="mail_timing_select" type="radio" name="mail_timing_select" value="by_week">週間毎
            </div>
            <div>
                <input v-model="mail_timing_select" type="radio" name="mail_timing_select" value="by_month">ヶ月毎
            </div>
        </div>
        <div v-bind:class="{edit_article_mail_timing_select:mail_on, edit_article_mail_timing_select_none: mail_off}">
            <div v-bind:class="{edit_article_mail_timing_by_day_none:mail_timing_not_by_day}">
                <select name="mail_timing_by_day">
                    @empty(old('mail_timing_by_day'))
                        @php
                        for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_DAY_LIMIT; $i++) 
                        {
                            if ($i === $mail_info['by_day']) {
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        }
                        @endphp
                    @else
                        @php
                        for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_DAY_LIMIT; $i++) 
                        {
                            if ($i === intval(old('mail_timing_by_day'))) {
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        }
                        @endphp
                    @endempty
                </select>日毎
            </div>
            <div v-bind:class="{edit_article_mail_timing_by_week_none:mail_timing_not_by_week}">
                <select name="mail_timing_by_week">
                    @empty(old('mail_timing_by_week'))
                        @php
                        for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_WEEK_LIMIT; $i++) 
                        {
                            if ($i === $mail_info['by_week']) {
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        }
                        @endphp
                    @else
                        @php
                        for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_WEEK_LIMIT; $i++) 
                        {
                            if ($i === intval(old('mail_timing_by_week'))) {
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        }
                        @endphp
                    @endempty
                </select>週間毎
            </div>
            <div v-bind:class="{edit_article_mail_timing_by_month_none:mail_timing_not_by_month}">
                <select name="mail_timing_by_month">
                    @empty(old('mail_timing_by_month'))
                        @php
                        for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_MONTH_LIMIT; $i++) 
                        {
                            if ($i === $mail_info['by_month']) {
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        }
                        @endphp
                    @else
                        @php
                        for($i=1; $i<=\RemindMailTimingConst::REMIND_MAIL_TIMING_MONTH_LIMIT; $i++) 
                        {
                            if ($i === intval(old('mail_timing_by_month'))) {
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }
                            else {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        }
                        @endphp
                    @endempty
                </select>ヶ月毎
            </div>
        </div>
        <div class="btn_vertical_align">
            <input type="submit" class="btn_primary_more_than_4_chars mr_5" value="更新する">
            <button class="btn_default_more_than_4_chars" onclick="javascript:location.href='https://readingtohabit.com/articles';return false;">キャンセルする</button>
        </div>
    </form>
</div>
@endsection
