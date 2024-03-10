@extends('Academy.Layouts.master')

@section('title', trans('admin.training.training'))
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
                <h4 class="mb-5 text-primary text-center">{{trans('admin.training.All users who joined the user training')}}</h4>
                @foreach($training->joins as $join)
                    <div class="col-md-4">
                        <div class="card" style="width: 18rem;">
                            <img src="{{$join->user->image}}" class="card-img-top" alt="...">
                            <div class="card-body">
                                <span class="card-text">{{trans('admin.training.name')}} :  {{$join->user->name}}</span>
                                <br>
                                <span class="card-text">{{trans('admin.training.phone')}} :  {{$join->user->phone}}</span>
                                <br>
                                <span class="card-text">{{trans('admin.training.gender')}}  :  {{$join->user->gender}}</span>
                                <p class="card-text text-primary">{{trans('admin.training.order number')}} :  {{$join->invoice->order_number}}</p>
                                <p class="card-text text-primary">{{trans('admin.training.amount')}} :  {{$join->invoice->amount}}</p>
                                  <button class="btn btn-warning">{{$join->invoice->status}}</button>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection



@push('js')

@endpush
