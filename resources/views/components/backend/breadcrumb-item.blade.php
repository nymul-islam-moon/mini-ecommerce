<li class="breadcrumb-item {{ $active ? 'active' : '' }}" @if($active) aria-current="page" @endif>
    @if (!$active && $href)
        <a href="{{ $href }}">
            @if($icon)
                <i class="{{ $icon }}"></i>
            @endif
            {{ $slot }}
        </a>
    @else
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    @endif
</li>
