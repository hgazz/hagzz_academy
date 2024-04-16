@extends('Academy.Layouts.master')

@section('title', trans('admin.training.training'))

@push('css')

@endpush




@section('content')
    <div class="middle-content container-xxl p-0">

        <!--  BEGIN BREADCRUMBS  -->
        <div class="secondary-nav">
            <div class="breadcrumbs-container" data-page-heading="Analytics">
                <header class="header navbar navbar-expand-sm">
                    <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                    </a>
                    <div class="d-flex breadcrumb-content">
                        <div class="page-header">

                            <div class="page-title">
                            </div>

                            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('academy.index') }}">{{ trans('admin.dashboard') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.training.training') }}</li>
                                </ol>
                            </nav>

                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->
        <div class="container mt-4">
            <div class="row">
                    @forelse($academyTrainings as $training)
                    <div class="col-md-4 mb-1">
                        <div class="card" style="width: 18rem;">
                            <img src="{{$training->sport->icon}}" class="card-img-top h-25" alt="{{$training->name}}" width="200px" height="120px">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{$training->name}}</h5>
                                <p class="card-text">{{$training->description}}</p>
                                <span class="text-primary">{{$training->start_date}}</span>
                                /
                                <span class="">{{$training->end_date}}</span>
                                <br>
                                <span class="text-muted mt-2">{{trans('admin.training.level')}} : {{$training->level}}</span>
                                /
                                <span class="text-muted">{{trans('admin.training.max player ')}}: {{$training->max_players}}</span>

                                <p class="text-muted">{{trans('admin.training.Coach')}}: {{$training->coach->name}}</p>
                                <a href="{{route('academy.booking.show',$training->id)}}" class="btn btn-outline-primary w-100">{{trans('admin.training.Show Details')}}</a>
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>
    </div>
@endsection



@push('js')

@endpush
