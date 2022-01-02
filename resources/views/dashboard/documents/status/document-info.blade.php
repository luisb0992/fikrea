{{-- Información del documento --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-info text-info"></i>
        @lang('Información del documento')
    </h5>
</div>

<div class="col-md-12">
    <div class="main-card card">

        <div class="card-body table-responsive">
            @include('dashboard.partials.documents-table',
                [
                    'documents' => [$document],
                ] 
            )
        </div>
    </div>
</div>
{{--/Información del documento --}}