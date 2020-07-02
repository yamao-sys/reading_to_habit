@extends('layouts.top')

@section('title', 'Readingtohabit')

@section('content')
<div class="main_visual_area">
    <div class="main_visual">
        <div class="main_visual_content_1">
            <span class="color_primary_top_page">読書で学んだこと</span>を行動に活かそう
        </div>
        <div class="main_visual_content_2">
            <span class="color_primary_top_page">リマインドメール</span>で学びを行動に活かせているか<br>
            <span class="color_primary_top_page">定期的に</span>振り返ろう
        </div>
        <div class="to_register_user_form_area">
            <a href="register_user_form" class="to_register_user_form">＞&ensp;ご登録はこちらから</a>
        </div>
    </div>
</div>

<div class="concept_area">
    <div class="concept_text">
    <span class="color_primary_top_page">読書での学び</span>を記録し、<span class="color_primary_top_page">定期的に思い出す</span>仕組みを作ろう。<br>
    だから、その学びを<span class="color_primary_top_page">行動に反映し、習慣化</span>できる！
    </div>
</div>

<div class="value_area">
    <div class="value_content">
        <div class="value_header">Readingtohabitとは？</div>
        <div class="value">
        読書で学んだことを記録するサービスです。<br>
        読書で学んだことを日々の生活で活かしたい、読書で感動した内容を常に思い出せるようにしたい、という読書好きな方に最適な環境を提供します。
        </div>
    </div>
    <div class="value_content">
        <div class="value_header">他の読書記録サービスとの違いは？</div>
        <div class="value">
        Readingtohabitは、記録した内容を定期的にメールでユーザー本人に送信する仕組みを提供しております。<br>
        定期的なメールにより、記録した内容を思い出せることで、「記録を書いて終わり、少し時間が経つと内容を忘れてしまう」を防ぎます。
        また、感動した内容を思い出したいタイミングで思い出すことが可能となります。
        </div>
    </div>
    <div class="value_content">
        <div class="value_header">Readingtohabitでできること</div>
        <div class="value_can">
            <div class="value_can_each">・記録した内容を一覧表示できます。</div>
            <div class="value_can_each">・検索機能により、記録した内容をすぐに探し出すことができます。</div>
            <div class="value_can_each">・気に入った記録はお気に入りに登録できます。</div>
            <div class="value_can_each">・記録ごとに、リマインドメールの配信の有無を指定できます。</div>
            <div class="value_can_each">・リマインドメールの配信タイミングを自分好みに調整できます。</div>
            <div class="value_can_each">・一度書いた記録でも、内容を更新できます。</div>
        </div>
    </div>
</div>

<div class="conversion_area">
    <div class="conversion">
        <div class="conversion_text">
        今だけ、無料で登録可能！！<br>
        読書を通じて、人生をより豊かにしていきましょう！
        </div>
        <div class="to_register_user_form_area">
            <a href="register_user_form" class="conversion_btn">＞&ensp;ご登録はこちらから</a>
        </div>
    </div>
</div>
@endsection
