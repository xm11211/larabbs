@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if (Session::has($msg))
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ Session::get($msg) }}
        </div>
    @endif
@endforeach