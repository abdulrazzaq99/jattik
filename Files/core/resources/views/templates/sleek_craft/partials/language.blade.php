@php
    $languages = App\Models\Language::all();
@endphp

<div class="language-box">
    <div class="custom--dropdown">
        <div class="custom--dropdown__selected dropdown-list__item">
            @foreach ($languages as $language)
                @if (session('lang') == $language->code)
                    <div class="thumb">
                        <img src="{{ getImage(getFilePath('language') . '/' . $language->image, getFileSize('language')) }}" alt="image">
                    </div>
                    <span class="text">{{ __($language->name) }}</span>
                @endif
            @endforeach
        </div>
        <ul class="dropdown-list">
            @foreach ($languages as $language)
                @if (session('lang') != $language->code)
                    <li class="dropdown-list__item langSel" data-value="{{ $language->code }}">
                        <div class="thumb">
                            <img src="{{ getImage(getFilePath('language') . '/' . $language->image, getFileSize('language')) }}" alt="image">
                        </div>
                        <span class="text">{{ __($language->name) }}</span>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
