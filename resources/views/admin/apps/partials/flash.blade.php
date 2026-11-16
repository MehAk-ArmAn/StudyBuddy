{{-- Shared, accessible notices for the Apps workspace. --}}

@if(session('status'))
    <div class="sb-note sb-note--good sb-toast sb-toast--success" role="status" data-admin-toast>
        <span class="sb-toast__icon" aria-hidden="true">
            <svg><use href="#sb-admin-icon-check"></use></svg>
        </span>
        <div class="sb-toast__copy">
            <strong>Changes saved</strong>
            <p>{{ session('status') }}</p>
        </div>
        <button type="button" class="sb-toast__close" aria-label="Dismiss notification" data-toast-dismiss>
            <svg aria-hidden="true"><use href="#sb-admin-icon-close"></use></svg>
        </button>
    </div>
@endif

@if($errors->any())
    <div class="sb-note sb-note--bad sb-toast sb-toast--error" role="alert" tabindex="-1" data-admin-toast data-validation-summary>
        <span class="sb-toast__icon" aria-hidden="true">!</span>
        <div class="sb-toast__copy">
            <strong>We couldn't save this app.</strong>
            <p>Check the highlighted fields below and try again.</p>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="sb-toast__close" aria-label="Dismiss error summary" data-toast-dismiss>
            <svg aria-hidden="true"><use href="#sb-admin-icon-close"></use></svg>
        </button>
    </div>
@endif
