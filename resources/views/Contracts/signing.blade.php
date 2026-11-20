<x-app-layout>
    <title>{{ env('APP_NAME') }} | Contract Signing</title>

    <!-- Page header with breadcrumbs -->
    <div class="page-header">
        <div class="page-header-content">
            @include('layouts.heading-elements')
        </div>
        <div class="breadcrumb-line breadcrumb-line-component">
            <ul class="breadcrumb">
                <li><a href="{{ route('dashboard') }}"><i class="icon-home2 position-left"></i>Home</a></li>
                <li><a href="{{ route('contracts.index') }}">Contracts Management</a></li>
                <li class="active">Contract Signing</li>
            </ul>
            <ul class="breadcrumb-elements">
                @include('layouts.help')
            </ul>
        </div>
    </div>
    <!-- /page header -->

    <!-- Content area -->
    <div class="content">
        <div class="panel panel-white" style="margin-bottom: 0;">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <i class="icon-pencil3 position-left"></i>
                    Digital Contract Signing
                </h6>
                <div class="heading-elements">
                    <a href="{{ route('contract.signing') }}" class="btn btn-sm btn-success" target="_blank" title="Open in clean fullpage view">
                        <i class="icon-enter"></i> Open Clean View
                    </a>
                    <button type="button" class="btn btn-sm btn-default" onclick="reloadFlutterApp()">
                        <i class="icon-reload-alt"></i> Reload
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="openFullscreen()">
                        <i class="icon-screen-full"></i> Fullscreen
                    </button>
                </div>
            </div>

            <div class="panel-body" style="padding: 0; margin: 0;">
                <!-- Embedded Flutter Web App -->
                <div id="flutter-container" style="position: relative; width: 100%; min-height: calc(100vh - 200px);">
                    <!-- Loading indicator -->
                    <div id="loading-indicator" style="
                        position: absolute;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        text-align: center;
                        z-index: 1;
                    ">
                        <i class="icon-spinner2 spinner" style="font-size: 48px; color: #2196F3;"></i>
                        <p style="margin-top: 15px; color: #666;">Loading Contract Signing App...</p>
                    </div>

                    <iframe
                        id="flutter-app-frame"
                        src="{{ route('contract.signing.app') }}"
                        style="
                            width: 100%;
                            height: calc(100vh - 200px);
                            border: none;
                            display: block;
                        "
                        title="Contract Signing Application"
                        allow="clipboard-read; clipboard-write"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
    <!-- /content area -->

    <style>
        #flutter-container {
            background: white;
        }
        #flutter-app-frame {
            opacity: 0;
            transition: opacity 0.3s ease-in;
        }
        #flutter-app-frame.loaded {
            opacity: 1;
        }
        .fullscreen-active {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 99999 !important;
            background: white !important;
        }
        .fullscreen-active iframe {
            height: 100vh !important;
        }
    </style>

    <script>
        const iframe = document.getElementById('flutter-app-frame');
        const loadingIndicator = document.getElementById('loading-indicator');

        iframe.addEventListener('load', function() {
            console.log('Flutter app iframe loaded');
            this.classList.add('loaded');
            setTimeout(function() {
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
            }, 500);
        });

        iframe.addEventListener('error', function() {
            if (loadingIndicator) {
                loadingIndicator.innerHTML = '<i class="icon-cross2" style="font-size: 48px; color: #f44336;"></i><p style="margin-top: 15px; color: #f44336;">Error loading app. Please reload.</p>';
            }
        });

        function reloadFlutterApp() {
            if (loadingIndicator) {
                loadingIndicator.style.display = 'block';
                loadingIndicator.innerHTML = '<i class="icon-spinner2 spinner" style="font-size: 48px; color: #2196F3;"></i><p style="margin-top: 15px; color: #666;">Loading Contract Signing App...</p>';
            }
            iframe.classList.remove('loaded');
            iframe.src = iframe.src;
        }

        function openFullscreen() {
            const container = document.getElementById('flutter-container');
            if (!document.fullscreenElement) {
                if (container.requestFullscreen) {
                    container.requestFullscreen();
                } else if (container.webkitRequestFullscreen) {
                    container.webkitRequestFullscreen();
                } else if (container.msRequestFullscreen) {
                    container.msRequestFullscreen();
                }
                container.classList.add('fullscreen-active');
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
                container.classList.remove('fullscreen-active');
            }
        }

        document.addEventListener('fullscreenchange', function() {
            const container = document.getElementById('flutter-container');
            if (!document.fullscreenElement) {
                container.classList.remove('fullscreen-active');
            }
        });

        window.addEventListener('message', function(event) {
            console.log('Message from Flutter app:', event.data);
            if (event.data.type === 'signature_complete') {
                // Optionally redirect to contracts list
            }
        });
    </script>
</x-app-layout>
