<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>
            @if($mod)
                {{ $mod->title }}&nbsp;|&nbsp;
            @endif

            @if($_SERVER['REQUEST_URI'] == '/mods/sn1')
                Subnautica Mods&nbsp;|&nbsp;
            @elseif($_SERVER['REQUEST_URI'] == '/mods/sbz')
                Subnautica: Below Zero Mods&nbsp;|&nbsp;
            @endif

            {{ env("APP_ENV") == "local" ? "Modnautica" : "Submodica" }}
        </title>

        <!--Set Favicon-->
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('img/favicon/apple-touch-icon.png')}}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('img/favicon/favicon-32x32.png')}}" />
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('img/favicon/favicon-16x16.png')}}" />
        <link rel="manifest" href="{{asset('img/favicon/site.webmanifest')}}" />

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        @if($mod)
            <meta property="og:title" content="{{ $mod->title }} by {{ $mod->creator }}" />
            <meta property="og:type" content="website" />
            <meta property="og:url" content="https://submodica.xyz/mods/{{ $mod->game }}/{{ $mod->id }}" />
            <meta property="og:image" content="{{ $mod->profile_image }}" />
            <meta property="og:description" content="{{ $mod->tagline }}" />
            <meta name="theme-color" content="#222D4C" />

            <meta name="description" content="{{ $mod->tagline }}" />

        @elseif(strlen($_SERVER['REQUEST_URI']) <= 1)
            <?php $desc = "Submodica is a community driven website for listing and finding amazing Subnautica and Subnautica: Below Zero mods!" ?>

            <meta property="og:title" content="Submodica" />
            <meta property="og:type" content="website" />
            <meta property="og:url" content="https://submodica.xyz/" />
            <meta property="og:image" content="https://submodica.b-cdn.net/Peeper.png" />
            <meta property="og:description" content="{{ $desc }}" />
            <meta name="theme-color" content="#222D4C" />

            <meta name="description" content="{{ $desc }}" />
        @endif

        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;900&display=swap" rel="stylesheet" />

        <script>
            window.development = {!! env("APP_ENV") == "local" ? 1 : 0 !!};
            window.user = {!! json_encode($user?:[]) !!};
        </script>

        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
