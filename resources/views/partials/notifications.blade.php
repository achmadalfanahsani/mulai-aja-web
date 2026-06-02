<div id="notification-container" style="position: fixed; top: 1.5rem; right: 1.5rem; z-index: 99999; width: 350px; max-width: 80vw;">
    {{-- BERHASIL STORE -> BLUE (info) --}}
    @if (session('success_store') || session('store_success'))
        <div class="alert alert-info fade show shadow border-0 mb-3" role="alert" style="background-color: #007bff; color: white;">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-plus-circle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('success_store') ?? session('store_success') }}
                </div>
            </div>
        </div>
    @endif

    {{-- BERHASIL UPDATE -> GREEN (success) --}}
    @if (session('success_update') || session('update_success') || session('success'))
        <div class="alert alert-success fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('success_update') ?? session('update_success') ?? session('success') }}
                </div>
            </div>
        </div>
    @endif

    {{-- BERHASIL DELETE -> RED (danger) --}}
    @if (session('success_delete') || session('delete_success'))
        <div class="alert alert-danger fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-trash-alt"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('success_delete') ?? session('delete_success') }}
                </div>
            </div>
        </div>
    @endif

    {{-- ERROR / DANGER --}}
    @if (session('error'))
        <div class="alert alert-danger fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-exclamation-circle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('error') }}
                </div>
            </div>
        </div>
    @endif

    {{-- INFO --}}
    @if (session('info'))
        <div class="alert alert-info fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('info') }}
                </div>
            </div>
        </div>
    @endif

    {{-- WARNING --}}
    @if (session('warning'))
        <div class="alert alert-warning fade show shadow border-0 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-2">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    {{ session('warning') }}
                </div>
            </div>
        </div>
    @endif
</div>
