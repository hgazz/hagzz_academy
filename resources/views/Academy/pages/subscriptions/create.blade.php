@extends('Academy.Layouts.master')

@section('title', 'Add Subscription')

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <form action="{{ route('academy.subscriptions.store') }}" method="POST">
                    @include('Academy.pages.subscriptions.partials._form')
                </form>
            </div>
        </div>
    </div>
@endsection
