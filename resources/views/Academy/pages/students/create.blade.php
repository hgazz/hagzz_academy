@extends('Academy.Layouts.master')

@section('title', trans('admin.student_management.add_student'))

@section('content')
    <div class="middle-content container-xxl p-0">
        <div class="row layout-top-spacing">
            <div class="col-12 layout-spacing">
                <form action="{{ route('academy.students.store') }}" method="POST">
                    @include('Academy.pages.students.partials._form')
                </form>
            </div>
        </div>
    </div>
@endsection
