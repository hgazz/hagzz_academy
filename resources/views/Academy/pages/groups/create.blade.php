@extends('Academy.Layouts.master')

@section('title', 'Add Group')

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <form action="{{ route('academy.groups.store') }}" method="POST">
                    @include('Academy.pages.groups.partials._form')
                </form>
            </div>
        </div>
    </div>
@endsection
