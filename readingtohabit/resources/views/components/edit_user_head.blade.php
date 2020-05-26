<div class="edit_user_head">
    @if ($edit_target === 'profile')
    <a class="edit_target_primary" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_profile">プロフィール編集</a>
    @else
    <a class="edit_target_default" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_profile">プロフィール編集</a>
    @endif
    
    @if ($edit_target === 'password')
    <a class="edit_target_primary" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_password">パスワード編集</a>
    @else
    <a class="edit_target_default" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_password">パスワード編集</a>
    @endif
    
    @if ($edit_target === 'default_mail_timing')
    <a class="edit_target_primary_bottom" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_default_mail_timing">デフォルト配信タイミング編集</a>
    @else
    <a class="edit_target_default_bottom" href="{{\DocumentRootConst::DOCUMENT_ROOT}}edit_default_mail_timing">デフォルト配信タイミング編集</a>
    @endif
</div>
