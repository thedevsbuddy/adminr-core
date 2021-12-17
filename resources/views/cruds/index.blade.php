@extends('liquid-lite::layouts.master')

@section('title', __('Manage Resources'))

@push('scopedCss')

@endpush

@section('content')
<div class="container-fluid" id="app">
    <div class="d-sm-flex justify-content-between align-items-center mb-3">
        <h3 class="text-dark mb-0">{{ __('Manage Resources') }}</h3>
        <div>
            <a href="{{ route('adminr.builder') }}" class="btn btn-primary btn-sm d-none d-sm-inline-block">
                <svg class="c-icon mr-1">
                    <use xlink:href="{{ coreUiIcon('cil-library-add') }}"></use>
                </svg>
                {{ __('Add new resource') }}
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <p class="card-title m-0">{{ __('All Resources List') }}</p>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-responsive-sm table-striped m-0">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 50px">#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Model') }}</th>
                            <th>{{ __('Controllers') }}</th>
                            <th>{{ __('Generated') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($cruds as $index => $crud)
                            <tr>
                                <td class="text-center">{{++$index}}</td>
                                <td>{{ $crud->name }}</td>
                                <td>{{ $crud->model }}</td>
                                <td>{{ $crud->controllers->admin }} (With API controller) </td>
                                <td>
                                    @if($crud->created_at->isToday())
                                        {{ $crud->created_at->diffForHumans() }}
                                    @elseif($crud->created_at->isYesterday())
                                        Yesterday at {{ $crud->created_at->format('h:i A') }}
                                    @else
                                        {{ $crud->created_at->format('jS M, Y') }}
                                    @endif
                                </td>
                                <td>
{{--                                    <a href="#" class="btn btn-sm btn-icon btn-primary" title="Edit">--}}
{{--                                        <svg class="h-3 w-3">--}}
{{--                                            <use xlink:href="{{ coreUiIcon('cil-pen') }}"></use>--}}
{{--                                        </svg>--}}
{{--                                    </a>--}}
                                    <a href="{{ route('adminr.cruds.configure', encrypt($crud->id)) }}" class="btn btn-sm btn-icon btn-primary" title="Configure Permissions">
                                        <svg class="h-3 w-3">
                                            <use xlink:href="{{ coreUiIcon('cil-cog') }}"></use>
                                        </svg>
                                    </a>
                                    <a href="#" data-form="crud_{{ $crud->id }}" class="btn btn-sm btn-icon btn-danger delete-item" title="Delete">
                                        <svg class="h-3 w-3">
                                            <use xlink:href="{{ coreUiIcon('cil-trash') }}"></use>
                                        </svg>
                                    </a>
                                    <form class="d-none" id="crud_{{ $crud->id }}" action="{{ route('adminr.cruds.destroy', $crud->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No Resources generated yet!</td></tr>
                        @endforelse
                        </tbody>
                        <tfoot>
                        {!! $cruds->appends($_GET)->links() !!}
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scopedJs')
@endpush
