@extends('layouts.error')

@section('title', 'Readingtohabit-一時的な障害')

<div class="content">
    <div class="error_page_body">
        <div class="error_page_header">
            一時的な障害が発生しております。
        </div>
        <div class="error_content">
            <div class="error_content_info">
                大変恐れ入りますが、時間を置いて再度お試しくださいませ。
            </div>
        </div>
    </div>
    <div class="mt_3">
        @if (session()->get('user_id'))
        &lt;&lt;&emsp;<a href="articles" class="link_primary">記録一覧画面へ</a>
        @else
        &lt;&lt;&emsp;<a href="login" class="link_primary">ログイン画面へ</a>
        @endif
    </div>
</div>
