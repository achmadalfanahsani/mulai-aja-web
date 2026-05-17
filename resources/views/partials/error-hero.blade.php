<div class="hero bg-body-extra-light">
    <div class="hero-inner">
        <div class="content content-full">
            <div class="py-4 text-center">

                <div class="display-1 fw-bold {{ $color }}">
                    @if(!empty($icon))
                        <i class="{{ $icon }} opacity-50 me-1"></i>
                    @endif
                    {{ $code }}
                </div>

                <h1 class="fw-bold mt-5 mb-2">
                    {{ $title ?? 'Oops.. You just found an error page..' }}
                </h1>

                <h2 class="fs-4 fw-medium text-muted mb-5">
                    {{ $message }}
                </h2>

                <a class="btn btn-lg btn-alt-secondary"
                   href="{{ url()->previous() ?? url('/') }}">
                    <i class="fa fa-arrow-left opacity-50 me-1"></i>
                    Back
                </a>

            </div>
        </div>
    </div>
</div>