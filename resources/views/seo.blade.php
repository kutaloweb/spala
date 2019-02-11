<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>{{ !empty($article->title) ? $article->title . ' | ' . config('config.company_name') : config('config.company_name')}}</title>
    <meta name="description" content="{{ $article->totally_stripped_body or config('config.company_description') }}">

    <meta property="og:type" content="article"/>
    <meta property="og:site_name" content="{{ config('config.company_name')}}"/>
    <meta property="og:url" content="{{ Request::url() }}"/>
    <meta property="og:title" content="{{ $article->title or config('config.company_name') }}"/>
    <meta property="og:description" content="{{ $article->totally_stripped_body or config('config.company_description') }}"/>
    <meta property="og:image" content="{{ !empty($article->cover) ? config('config.app_url') . '/' . $article->cover : config('config.app_url') . '/' . config('config.default_cover') }}"/>

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="{{ $article->totally_stripped_body or config('config.company_description') }}"/>
    <meta name="twitter:title" content="{{ $article->title or config('config.company_name') }}"/>
    <meta name="twitter:image" content="{{ !empty($article->cover) ? config('config.app_url') . '/' . $article->cover : config('config.app_url') . '/' . config('config.default_cover') }}"/>
</head>
<body>

<div>
    <h1>{{ $article->title or config('config.company_name') }}</h1>
    <div>{!! $article->stripped_body or config('config.company_description') !!}</div>
</div>
</body>
</html>
