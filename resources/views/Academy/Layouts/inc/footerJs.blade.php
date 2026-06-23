<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('assetsAdmin/src/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assetsAdmin/src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assetsAdmin/src/plugins/src/mousetrap/mousetrap.min.js') }}"></script>
<script src="{{ asset('assetsAdmin/src/plugins/src/waves/waves.min.js') }}"></script>
<script src="{{ asset('assetsAdmin/layouts/vertical-light-menu/app.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset('assetsAdmin/confirmationDelete.js') }}"></script>
<script src="{{ asset('assetsAdmin/yajaraLog.js') }}"></script>
@if (session()->has('success'))
    <script>
        console.info('[Hagzz] Operation completed', @json(session('success')));
        Swal.fire({
            icon: 'success',
            text: @json(session('success')),
            confirmButtonText: 'OK'
        });
    </script>
@endif

@if (session()->has('error'))
    <script>
        console.error('[Hagzz] Operation failed', @json(session('error')));
        Swal.fire({
            icon: 'error',
            text: @json(session('error')),
            confirmButtonText: 'OK'
        });
    </script>
@endif

@if ($errors->any())
    <script>
        console.error('[Hagzz] Validation failed', @json($errors->toArray()));
        Swal.fire({
            icon: 'error',
            text: @json($errors->first()),
            confirmButtonText: 'OK'
        });
    </script>
@endif

@stack('js')
