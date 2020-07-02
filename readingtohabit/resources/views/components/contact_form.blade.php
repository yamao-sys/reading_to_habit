<form action="contact_check" method="post">
    {{ csrf_field() }}
    <div class="form_element">
        <label>メールアドレス</label>
        @if (empty(session()->get('contact_info_email')))
        <input type="email" name="email" class="form_input" value="{{old('email')}}">
        @else
        <input type="email" name="email" class="form_input" value="{{session()->get('contact_info_email')}}">
        @endif
        @isset($errors)
            @if ($errors->has('email'))
            <div class="error_msg">
                <p>{{ $errors->first('email') }}</p>
            </div>
            @endif
        @endisset
    </div>
    <div class="form_element">
        <label>お問い合わせ内容</label>
        @if (empty(session()->get('contact_info_contact')))
        <textarea name="contact" class="form_textarea">{{old('contact')}}</textarea>
        @else
        <textarea name="contact" class="form_textarea">{{session()->get('contact_info_contact')}}</textarea>
        @endif
        @isset($errors)
            @if ($errors->has('contact'))
            <div class="error_msg">
                <p>{{ $errors->first('contact') }}</p>
            </div>
            @endif
        @endisset
    </div>
    <input type="submit" class="btn_primary_more_than_4_chars" value="内容確認画面へ">
</form>
