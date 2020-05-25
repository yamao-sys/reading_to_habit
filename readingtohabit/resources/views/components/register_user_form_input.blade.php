<div class="form_element">
    @if ($input == 'name')
        <label>ユーザー名</label>
        @empty ($register_user_info['name'])
        <input type="text" class="form_input" name="{{ $input }}" value="{{old('name')}}">
        @else
        <input type="text" class="form_input" name="{{ $input }}" value="{{ $register_user_info['name'] }}">
        @endempty
    @elseif ($input == 'email')
        <label>メールアドレス</label>
        @empty ($register_user_info['email'])
        <input type="email" class="form_input" name="{{ $input }}" value="{{old('email')}}">
        @else
        <input type="email" class="form_input" name="{{ $input }}" value="{{ $register_user_info['email'] }}">
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
