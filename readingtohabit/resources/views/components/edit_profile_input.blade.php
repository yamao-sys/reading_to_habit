@if ($input === 'profile_img')
<div class="form_element">
    <label>プロフィール画像</label>
    <div class="edit_profile_img_area">
        <label for="filename">
            <span class="file_input">ファイルを選択</span><input v-on:change="setImage()" type="file" id="filename" name="profile_img" value="{{empty(old('profile_img')) ? '': old('profile_img')}}">
        </label>

        <div class="edit_profile_img_content">
            @empty(old('profile_img'))
                <img v-if="data.image === ''" src="{{\DocumentRootConst::DOCUMENT_ROOT}}{{session()->get('profile_img')}}?{{session()->get('current_date')}}" class="edit_profile_img object-fit-img" id="profile_img object-fit-img">
                <img v-else v-bind:src="data.image" class="edit_profile_img object-fit-img" id="profile_img">
            @else
            <img v-if="data.image === ''" src="{{old('profile_img')}}" class="edit_profile_img object-fit-img" id="profile_img">
            <img v-else v-bind:src="data.image" class="edit_profile_img object-fit-img" id="profile_img">
            @endempty
        </div>
    </div>
    @isset($errors)
        @if ($errors->has('profile_img'))
        <div class="error_msg">
            <p>{{ $errors->first('profile_img')}}</p>
        </div>
        @endif
    @endisset
</div>
@endif

@if ($input === 'name')
<div class="form_element">
    <label>ユーザー名</label>
    @empty(old('name'))
    <input type="text" name="name" class="form_input" value="{{$profile['name']}}">
    @else
    <input type="text" name="name" class="form_input" value="{{old('name')}}">
    @endempty
    @isset($errors)
        @if ($errors->has('name'))
        <div class="error_msg">
            <p>{{ $errors->first('name')}}</p>
        </div>
        @endif
    @endisset
</div>
@endif

@if ($input === 'email')
<div class="form_element">
    <label>メールアドレス</label>
    @empty(old('email'))
    <input type="email" name="email" class="form_input" value="{{$profile['email']}}">
    @else
    <input type="email" name="email" class="form_input" value="{{old('email')}}">
    @endempty
    @isset($errors)
        @if ($errors->has('email'))
        <div class="error_msg">
            <p>{{ $errors->first('email')}}</p>
        </div>
        @endif
    @endisset
</div>
@endif
