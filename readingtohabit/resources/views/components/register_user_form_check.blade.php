<div class="form_element">
    @if ($input == 'name')
        <label>ユーザー名</label>
        <div class="register_user_form_check">{{ $user_info['name'] }}</div>
    @elseif ($input == 'email')
        <label>メールアドレス</label>
        <div class="register_user_form_check">{{ $user_info['email'] }}</div>
    @elseif ($input == 'password')
        <label>パスワード</label>
        <div class="register_user_form_check">{{ $user_info['password'] }}</div>
    @endif
</div>
