<div id="notification-container" style="position: fixed; top: 1.5rem; right: 1.5rem; z-index: 99999; width: 350px; max-width: 80vw;">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('info') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('warning') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
</div>
