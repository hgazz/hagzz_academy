@extends('Academy.Layouts.master')

@section('title', trans('admin.training.calendar'))

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
{{--    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>--}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
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
                                    <li class="breadcrumb-item active" aria-current="page">{{ trans('admin.clasess.clasess') }}</li>
                                </ol>
                            </nav>

                        </div>
                    </div>
                </header>
            </div>
        </div>
        <!--  END BREADCRUMBS  -->

        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3>{{ trans('admin.training.calendar') }}</h3>
                        </div>

                    </div>
                    <div class="card-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
{{--    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.15/index.global.min.js"></script>--}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function() {--}}
{{--            var calendarEl = document.getElementById('calendar');--}}
{{--            var events = @json($events); // Pass the events data to JavaScript--}}

{{--            var calendar = new FullCalendar.Calendar(calendarEl, {--}}
{{--                initialView: 'timeGridWeek',--}}
{{--                headerToolbar: {--}}
{{--                    left: 'prev,next today',--}}
{{--                    center: 'title',--}}
{{--                    right: 'dayGridMonth,timeGridWeek,timeGridDay'--}}
{{--                },--}}
{{--                events: events,--}}
{{--                eventContent: function(arg) {--}}
{{--                    return { html: arg.event.title };--}}
{{--                }--}}
{{--            });--}}

{{--            calendar.render();--}}
{{--        });--}}
{{--    </script>--}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function () {--}}
{{--            var calendarEl = document.getElementById('calendar');--}}
{{--            var allEvents = @json($events);--}}

{{--            // تحويل أسماء الأيام إلى أرقام الأيام في الأسبوع--}}
{{--            function getDayIndex(dayName) {--}}
{{--                var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];--}}
{{--                return days.indexOf(dayName.toLowerCase());--}}
{{--            }--}}

{{--            // إنشاء أحداث لكل يوم في classes_days--}}
{{--            function createEventsForDays(events) {--}}
{{--                var eventSources = [];--}}
{{--                events.forEach(function (event) {--}}
{{--                    event.days.forEach(function (day) {--}}
{{--                        var dayIndex = getDayIndex(day);--}}
{{--                        if (dayIndex !== -1) {--}}
{{--                            var newEvent = Object.assign({}, event);--}}
{{--                            newEvent.daysOfWeek = [dayIndex]; // تعيين يوم الأسبوع لتكرار الحدث--}}
{{--                            newEvent.startTime = event.start.split('T')[1]; // تعيين وقت البداية--}}
{{--                            newEvent.endTime = event.end.split('T')[1]; // تعيين وقت النهاية--}}
{{--                            newEvent.background= event.color;--}}
{{--                            eventSources.push(newEvent);--}}
{{--                        }--}}
{{--                    });--}}
{{--                });--}}
{{--                return eventSources;--}}
{{--            }--}}

{{--            // إنشاء أحداث لكل يوم في classes_days--}}
{{--            var eventSources = createEventsForDays(allEvents);--}}

{{--            // تهيئة FullCalendar باستخدام مصادر الأحداث--}}
{{--            var calendar = new FullCalendar.Calendar(calendarEl, {--}}
{{--                initialView: 'dayGridMonth',--}}
{{--                events: eventSources,--}}
{{--                eventTimeFormat: { // تنسيق الوقت لعرض وقت البداية والنهاية--}}
{{--                    hour: '2-digit',--}}
{{--                    minute: '2-digit',--}}
{{--                    meridiem: true--}}
{{--                },--}}
{{--                eventBackgroundColor: function(info) {--}}
{{--                    return info.event.extendedProps.color;--}}
{{--                },--}}
{{--                eventColor: function(info) {--}}
{{--                    return info.event.extendedProps.color;--}}
{{--                }--}}
{{--            });--}}

{{--            // عرض التقويم--}}
{{--            calendar.render();--}}
{{--        });--}}
{{--    </script>--}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var allEvents = @json($events);

        // تحويل أسماء الأيام إلى أرقام الأيام في الأسبوع
        function getDayIndex(dayName) {
            var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            return days.indexOf(dayName.toLowerCase());
        }

        // إنشاء أحداث لكل يوم في classes_days
        function createEventsForDays(events) {
            var eventSources = [];
            events.forEach(function (event) {
                event.days.forEach(function (day) {
                    var dayIndex = getDayIndex(day);
                    if (dayIndex !== -1) {
                        var newEvent = Object.assign({}, event);
                        newEvent.daysOfWeek = [dayIndex]; // تعيين يوم الأسبوع لتكرار الحدث
                        newEvent.startTime = event.start.split('T')[1]; // تعيين وقت البداية
                        newEvent.endTime = event.end.split('T')[1]; // تعيين وقت النهاية
                        newEvent.background = event.color; // تعيين اللون الخلفي
                        eventSources.push(newEvent);
                    }
                });
            });
            return eventSources;
        }

        // إنشاء أحداث لكل يوم في classes_days
        var eventSources = createEventsForDays(allEvents);

        // تهيئة FullCalendar باستخدام مصادر الأحداث
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: eventSources,
            eventTimeFormat: { // تنسيق الوقت لعرض وقت البداية والنهاية
                hour: '2-digit',
                minute: '2-digit',
                meridiem: true
            },
            eventBackgroundColor: function(info) {
                return info.event.extendedProps.color;
            },
            eventColor: function(info) {
                return info.event.extendedProps.color;
            },
            eventContent: function(arg) {
                // Customizing the event title with background color
                var title = arg.event.title;
                var startTime = arg.event.start;
                var backgroundColor = arg.event.extendedProps.background || '#ffffff'; // Set default background color if not provided

                // Format the start time to only show hours and minutes in 2-digit format
                var hours = startTime.getHours().toString().padStart(2, '0');  // Ensure 2-digit hours
                var minutes = startTime.getMinutes().toString().padStart(2, '0');  // Ensure 2-digit minutes
                var formattedTime = hours + ':' + minutes;  // Combine hours and minutes

                return {
                    html: '<div style="background-color: ' + backgroundColor + '; padding: 5px; border-radius: 5px; color: white;">' + title + ' ' + formattedTime + '</div>'
                };
            }
        });

        // عرض التقويم
        calendar.render();
    });
</script>
@endpush

