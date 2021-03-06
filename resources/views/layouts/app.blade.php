<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf_token" content="{{ csrf_token() }}">

    <title>@yield('title', 'LaraBBS') - {{ setting('site_name', 'Laravel 进阶教程') }}</title>
    <meta name="description" content="@yield('description', setting('seo_description', 'LaraBBS 爱好者社区。'))" />
    <meta name="keyword" content="@yield('keyword', setting('seo_keyword', 'LaraBBS,社区,论坛,开发者论坛'))" />

    <!-- Styles -->
    {{--asset('css/app.css') 使用当前请求的协议（ HTTP 或 HTTPS ）为资源文件生成一个 URL，以我们的情况，
    会生成 http://larabbs.test/css/app.css--}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>

<body>
{{--route_class()会将当前请求的路由名称转换为 CSS 类名称，作用是允许我们针对某个页面做页面样式定制--}}
    <div id="app" class="{{ route_class() }}-page">

        @include('layouts._header')

        <div class="container">

            @include('layouts._message')
            @yield('content')

        </div>

        @include('layouts._footer')
    </div>

    @if (app()->isLocal())
        @include('sudosu::user-selector')
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>