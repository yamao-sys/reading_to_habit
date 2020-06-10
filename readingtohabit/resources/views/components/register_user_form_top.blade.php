<div class="register_user_form_top">
    <div class="register_user_phase_area">
        @isset ($phase)
        <div class="register_user_phase_content">
            <div class="phase">
                登録情報入力
            </div>
            @if ($phase == 'input')
            <div class="register_user_in_phase">
            </div>
            @else
            <div class="register_user_default">
            </div>
            @endif
        </div>
        <div class="register_user_phase_content">
            <div class="phase">
                登録情報確認
            </div>
            @if ($phase == 'check')
            <div class="register_user_in_phase">
            </div>
            @else
            <div class="register_user_default">
            </div>
            @endif
        </div>
        <div class="register_user_phase_content">
            <div class="phase">
                登録完了
            </div>
            @if ($phase == 'finish')
            <div class="register_user_in_phase">
            </div>
            @else
            <div class="register_user_default">
            </div>
            @endif
        </div>
    </div>
    @if ($phase != 'finish')
    <div class="register_free">
        今なら、登録無料！！
    </div>
    @else
    <div class="register_finish">
        ユーザー登録が完了しました！！
    </div>
    @endif
    @endisset
</div>
