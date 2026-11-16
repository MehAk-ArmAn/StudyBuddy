@auth
    @if((bool) auth()->user()?->is_admin)
        <a
            class="sb-admin-mailing-list-link"
            href="{{ route('admin.control-room.mailing-list.index') }}"
        >
            <span>Mailing list</span>
        </a>
    @endif
@endauth
