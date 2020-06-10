@if ($input === 'current_password')
<div class="form_element">
    <label>現在のパスワード</label>
    <input type="password" name="current_password" class="form_input">
    @isset($errors)
        @if ($errors->has('current_password'))
        <div class="error_msg">
            <p>{{ $errors->first('current_password')}}</p>
        </div>
        @endif
    @endisset
</div>
@endif

@if($input === 'new_password')
<div class="form_element">
    <label>新しいパスワード</label>
    <input type="password" name="new_password" class="form_input">
    @isset($errors)
        @if ($errors->has('new_password'))
        <div class="error_msg">
            <p>{{ $errors->first('new_password')}}</p>
        </div>
        @endif
    @endisset
</div>
@endif

@if ($input === 'new_password_to_check')
<div class="form_element">
    <label>新しいパスワード確認用</label>
    <input type="password" name="new_password_to_check" class="form_input">
    @isset($errors)
        @if ($errors->has('new_password_to_check'))
        <div class="error_msg">
            <p>{{ $errors->first('new_password_to_check')}}</p>
        </div>
        @endif
    @endisset
</div>
@endif
