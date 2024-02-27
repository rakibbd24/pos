<?php $setting = DB::table('settings')->where('deleted_at', '=', null)->first(); ?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel=icon href={{ asset('images/logo.svg') }}>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Posly - POS with Inventory Management</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
        @yield('before-css')
        {{-- theme css --}}

        {{-- App Css for custom style --}}
        <link  rel="stylesheet" href="{{ asset('assets/fonts/iconsmind/iconsmind.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/styles/css/themes/lite-purple.min.css') }}">

        <link rel="stylesheet" href="{{asset('assets/styles/vendor/toastr.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/vendor/vue-select.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/vendor/sweetalert2.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/vendor/nprogress.css')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/theme.min.css" integrity="sha512-hbs/7O+vqWZS49DulqH1n2lVtu63t3c3MTAn0oYMINS5aT8eIAbJGDXgLt6IxDHcWyzVTgf9XyzZ9iWyVQ7mCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        {{-- axios js --}}
        <script src="{{ asset('assets/js/axios.js') }}"></script>
        {{-- vue select js --}}
        <script src="{{ asset('assets/js/vue-select.js') }}"></script>
        <script defer src="{{ asset('assets/js/compact-layout.js') }}"></script>

        {{-- Alpine Js --}}
        <script defer src="{{ asset('js/plugin-core/alpine-collapse.js') }}"></script>
        <script defer src="{{ asset('js/plugin-core/alpine.js') }}"></script>
        <script src="{{ asset('js/plugin-script/alpine-data.js') }}"></script>
        <script src="{{ asset('js/plugin-script/alpine-store.js') }}"></script>
        {{-- page specific css --}}
        @yield('page-css')
    </head>

    <body class="text-left">
        <!-- Pre Loader Strat  -->
        <div class='loadscreen' id="preloader">
            <div class="loader spinner-bubble spinner-bubble-primary"></div>
        </div>
        <!-- Pre Loader end  -->

        <!-- ============ Vetical SIdebar Layout start ============= -->
        @include('layouts.new-sidebar.master')
        <!-- ============ Vetical SIdebar Layout End ============= -->

        {{-- vue js --}}
        <script src="{{ asset('assets/js/vue.js') }}"></script>

        <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>

        <script src="{{asset('assets/js/vee-validate.min.js')}}"></script>
        <script src="{{asset('assets/js/vee-validate-rules.min.js')}}"></script>
        <script src="{{asset('/assets/js/moment.min.js')}}"></script>

        {{-- sweetalert2 --}}
        <script src="{{asset('assets/js/vendor/sweetalert2.min.js')}}"></script>


        {{-- common js --}}
        <script src="{{ asset('assets/js/common-bundle-script.js') }}"></script>
        {{-- page specific javascript --}}
        @yield('page-js')

        <script src="{{ asset('assets/js/script.js') }}"></script>

        <script src="{{asset('assets/js/vendor/toastr.min.js')}}"></script>

        <script src="{{asset('assets/js/nprogress.js')}}"></script>

        <script src="{{ asset('assets/js/tooltip.script.js') }}"></script>

        <script type="text/javascript" src="<?php echo asset('assets/js/pdfmake_arabic.min.js') ?>"></script>
        <script type="text/javascript" src="<?php echo asset('assets/js/vfs_fonts_arabic.js') ?>"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/fileuploader/plupload.full.min.js') }}"></script>
        <script type="text/javascript">
            @if(session('successMessage'))
                toastr.success(session('successMessage'));
            @endif
            // Custom example logic
            var upload_url = "{{ route('file-upload') }}";
            var uploader = new plupload.Uploader({
                runtimes : 'html5,flash,silverlight,html4',

                browse_button : 'pickfiles', // you can pass in id...
                container: document.getElementById('container'), // ... or DOM Element itself
                dragdrop: true,
                // add X-CSRF-TOKEN in headers attribute to fix this issue
                headers: {
                    'X-CSRF-TOKEN': document.getElementById("csrf").value
                },
                url : upload_url,
                filters : {
                    max_file_size : '10mb',
                    mime_types: [
                        {title : "Image files", extensions : "jpg,gif,png"},
                        {title : "Zip files", extensions : "zip"},
                        {title : "Files", extensions : "pdf"}
                    ]
                },
                // Flash settings
                flash_swf_url : '/plupload/js/Moxie.swf',
                // Silverlight settings
                silverlight_xap_url : '/plupload/js/Moxie.xap',
                init: {
                    // PostInit: function() {
                    //     document.getElementById('filelist').innerHTML = '';

                    //     document.getElementById('uploadfiles').onclick = function() {
                    //         uploader.start();
                    //         return false;
                    //     };
                    // },

                    FilesAdded: function(up, files) {
                        uploader.start();
                        plupload.each(files, function(file) {
                            document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                        });
                    },

                    UploadProgress: function(up, file) {
                        document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                    },

                    Error: function(up, err) {
                        console.log(err);
                        document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
                    }
                }
            });

            uploader.init();

        </script>
        @cloudinaryJS
        @yield('bottom-js')
    </body>
</html>
