<li class="mdc-list-item">
    @if(preg_match("/\.pdf$/im", $file))
    <a class="mdc-list-item__graphic material-icons"
        aria-hidden="true"
        href="{{ file_url($file) }}"
        target="_blank">picture_as_pdf</a>
    @else
    <a href="{{ file_url($file) }}"
        class="list-icon mdc-list-item__graphic"
        target="_blank"
        aria-hidden="true"
        style="background-image: url({{ file_url($file) }})"></a>
    @endif
    <a href="{{ file_url($file) }}"
        target="_blank"
        class="mdc-list-item__text"
        title="{{ $observation }}">
        <span class="mdc-list-item__primary-text status-{{$status}}">{{ status_message($status, true) }}</span>
        <span class="mdc-list-item__secondary-text">
            <time datetime="{{$createdAt}}">{{ time_ago($createdAt) }}<time>
            @if($status == 'observed')
            - <span>{{ $observation }}</span>
            @endif
        </span>
    </a>
    <span aria-hidden="true"
        class="mdc-list-item__meta">
        @if($status == 'pending')
            <button
                class="btn-actions mdc-icon-button material-icons mdc-ripple-upgraded--unbounded mdc-ripple-upgraded"
                tabindex="-1"
                style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:1.71429; --mdc-ripple-left:10px; --mdc-ripple-top:10px;"
                data-status="{{ $status }}"
                data-type="{{ $type }}"
                data-id="{{ $id }}"
                data-observation="{{ $observation }}">
                assignment_turned_in
            </button>
        @else
            <button
                class="btn-notify-email mdc-icon-button material-icons mdc-ripple-upgraded--unbounded mdc-ripple-upgraded"
                tabindex="-1"
                style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:1.71429; --mdc-ripple-left:10px; --mdc-ripple-top:10px;"
                data-type="{{ $type }}"
                data-id="{{ $id }}">forward_to_inbox</button>
            @if($status != 'approved')
                <button
                    class="btn-actions mdc-icon-button material-icons mdc-ripple-upgraded--unbounded mdc-ripple-upgraded"
                    tabindex="-1"
                    style="--mdc-ripple-fg-size:28px; --mdc-ripple-fg-scale:1.71429; --mdc-ripple-left:10px; --mdc-ripple-top:10px;"
                    data-id="{{ $id }}"
                    data-status="{{ $status }}"
                    data-observation="{{ $observation }}"
                    data-type="{{ $type }}">edit</button>
            @endif
        @endif
    </span>
</li>
