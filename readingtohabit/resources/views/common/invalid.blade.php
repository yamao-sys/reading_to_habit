@extends('layouts.error')

@section('title', 'Readingtohabit-無効なURL')

<div class="content">
    <div class="error_page_body">
        <div class="error_page_header">
            アクセスされたURLは無効なURLです。
        </div>
        <div class="error_content mb_2rem">
            <div class="error_content_title">
                URLの有効期限が切れている
            </div>
            <div class="error_content_info">
                メールが送信されてから24時間以上経過している場合はお送りしたURLの有効期限が切れておりますでので、
                パスワードの再設定より再度お手続きをお願いいたします。
            </div>
        </div>
        <div class="error_content">
            <div class="error_content_title">
                存在しない記録へアクセスしようとしている
            </div>
            <div class="error_content_info">
                対象の記録が合っているかどうかをご確認の上、
                アクセスしていただけますよう、よろしくお願いいたします。
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
