<div class="form_element">
    @if ($input == 'name')
        <label>ユーザー名</label>
        @empty (session()->get('register_user_info_name'))
        <input type="text" class="form_input" name="{{ $input }}" value="{{old('name')}}">
        @else
        <input type="text" class="form_input" name="{{ $input }}" value="{{ session()->get('register_user_info_name') }}">
        @endempty
    @elseif ($input == 'email')
        <label>メールアドレス</label>
        @empty (session()->get('register_user_info_name'))
        <input type="email" class="form_input" name="{{ $input }}" value="{{old('email')}}">
        @else
        <input type="email" class="form_input" name="{{ $input }}" value="{{ session()->get('register_user_info_email') }}">
        @endempty
    @elseif ($input == 'password')
        <label>パスワード</label>
        <input type="password" class="form_input" name="{{ $input }}">
    @elseif ($input == 'password_to_check')
        <label>パスワード確認用</label>
        <input type="password" class="form_input" name="{{ $input }}">
    @endif
    @empty ($errors)
    @else
        @if ($errors->has($input))
        <div class="error_msg">
            <p>{{ $errors->first($input) }}</p>
        </div>
        @endif
    @endempty
</div>
