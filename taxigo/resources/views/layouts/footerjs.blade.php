<!-- Required Js -->
<script src="{{ asset('build/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('build/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('build/js/pcoded.js') }}"></script>
<script src="{{ asset('build/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('build/js/jquery.min.js') }}"></script>
<script src="{{ asset('build/js/glightbox.js') }}"></script>
<script src="{{ asset('build/js/parsaly.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/dataTables.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
{{-- <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script> --}}
<script src="{{ asset('build/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/datepicker-full.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/flatpickr.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('build/js/plugins/wizard.min.js') }}"></script>
<script src="{{ URL::asset('build/js/plugins/tinymce/tinymce.min.js') }}"></script>
<script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
@yield('scripts')

<script>
    function getCurrencySign() {
        var currencySign = "{{ getCurrencySign() }}";
        return currencySign;
    }

    function logout(e) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't logout!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, Proceed!"
        }).then((result) => {
            if (result.isConfirmed) {
                // A real navigation, not an AJAX call: admin.logout responds with
                // a 302 redirect to the login page, which an AJAX request would
                // just follow silently in the background (destroying the session
                // server-side without ever moving this tab off the dashboard).
                window.location.href = '{{ route('admin.logout') }}';
            }
        });
    }
</script>
@if (env('APP_DARK_LAYOUT') == 'default')
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            dark_layout = 'true';
        } else {
            dark_layout = 'false';
        }
        layout_change_default();
        if (dark_layout == 'true') {
            layout_change('dark');
        } else {
            layout_change('light');
        }
    </script>
@endif

@if (env('APP_DARK_LAYOUT') != 'default')
    @if (env('APP_DARK_LAYOUT') == 'true')
        <script>
            layout_change('dark');
        </script>
    @endif
    @if (env('APP_DARK_LAYOUT') == false)
        <script>
            layout_change('light');
        </script>
    @endif
@endif


@if (env('APP_DARK_NAVBAR') == 'true')
    <script>
        layout_sidebar_change('dark');
    </script>
@endif

@if (env('APP_DARK_NAVBAR') == false)
    <script>
        layout_sidebar_change('light');
    </script>
@endif

@if (env('APP_BOX_CONTAINER') == false)
    <script>
        change_box_container('true');
    </script>
@endif

@if (env('APP_BOX_CONTAINER') == false)
    <script>
        change_box_container('false');
    </script>
@endif

@if (env('APP_CAPTION_SHOW') == 'true')
    <script>
        layout_caption_change('true');
    </script>
@endif

@if (env('APP_CAPTION_SHOW') == false)
    <script>
        layout_caption_change('false');
    </script>
@endif

@if (env('APP_RTL_LAYOUT') == 'true')
    <script>
        layout_rtl_change('true');
    </script>
@endif

@if (env('APP_RTL_LAYOUT') == false)
    <script>
        layout_rtl_change('false');
    </script>
@endif

@if (env('APP_PRESET_THEME') != '')
    <script>
        preset_change("{{ env('APP_PRESET_THEME') }}");
    </script>
@endif
<script>
    function markAsAllRead() {
        fetch("{{ route('admin.notifications.markAllRead') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    confirmButtonText: 'OK',
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                });
                console.error('Error:', error);
            });
    }
</script>
