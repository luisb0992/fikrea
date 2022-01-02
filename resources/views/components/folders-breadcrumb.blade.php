@props(['folder', 'leafNavigation' => false])
@php( $count = request()->get('count') )

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard.file.list', ['count' => $count]) }}">@lang('PRINCIPAL')</a>
        </li>
        @foreach( $folder->full_path ?? [] as $id => $path )
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.file.list', ['id' => $id, 'count' => $count]) }}">{{ $path }}</a>
            </li>
        @endforeach
        <li class="breadcrumb-item active" aria-current="page">
            @if( false !== $leafNavigation )
                <a href="{{ route('dashboard.file.list', ['id' => $folder->id, 'count' => $count]) }}">{{ $folder->name }}</a>
            @else
                {{ $folder->name }}
            @endif
            @if( $folder->notes )
                <div class="btn btn-sm btn-action-details ml-2" data-html="true" data-toggle="tooltip"
                     data-placement="bottom" data-original-title="{{ $folder->notes }}">
                    <i class="fa fa-eye fa-xs"></i>
                </div>
            @endif
        </li>
    </ol>
</nav>
