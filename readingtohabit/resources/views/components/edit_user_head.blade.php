<div class="edit_user_head">
    @if ($edit_target === 'profile')
    <a class="edit_target_primary" href="edit_profile">プロフィール編集</a>
    @else
    <a class="edit_target_default" href="edit_profile">プロフィール編集</a>
    @endif
    
    @if ($edit_target === 'password')
    <a class="edit_target_primary" href="edit_password">パスワード編集</a>
    @else
    <a class="edit_target_default" href="edit_password">パスワード編集</a>
    @endif
    
    @if ($edit_target === 'default_mail_timing')
    <a class="edit_target_primary_bottom" href="edit_default_mail_timing">デフォルト配信タイミング編集</a>
    @else
    <a class="edit_target_default_bottom" href="edit_default_mail_timing">デフォルト配信タイミング編集</a>
    @endif
</div>
