@extends('Academy.Layouts.master')

@section('title', 'Edit Student')

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <form action="{{ route('academy.students.update', $student) }}" method="POST">
                    @method('PUT')
                    @include('Academy.pages.students.partials._form')
                </form>
            </div>
        </div>
    </div>
@endsection
