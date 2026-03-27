@php
    $title = $metaData['meta_title']?->value ?? config('app.name');
    $description = $metaData['meta_description']?->value ?? config('app.name') . ' — best platform for your needs.';
    $image = \App\CentralLogics\Helpers::get_full_url(
        'landing/meta_image',
        $metaData['meta_image']?->value ?? '',
        $metaData['meta_image']?->storage[0]?->value ?? 'public',
        'upload_image'
    );
    $url = url()->current();
@endphp

<!-- ==================== BASIC SEO (Google, Bing, etc.) ==================== -->

<meta name="description" content="{{ $description }}">
<meta name="robots" content="index, follow">
<meta name="author" content="{{ config('app.name') }}">
<link rel="canonical" href="{{ $url }}">

<!-- ==================== OPEN GRAPH (Facebook, LinkedIn, WhatsApp, etc.) ==================== -->
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="{{ app()->getLocale() }}">

<!-- ==================== FACEBOOK ==================== -->
<meta property="fb:app_id" content="{{ config('services.facebook.app_id') ?? '' }}">
<meta property="og:updated_time" content="{{ now()->toIso8601String() }}">

<!-- ==================== TWITTER ==================== -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
<meta name="twitter:url" content="{{ $url }}">
<meta name="twitter:site" content="{{ config('services.twitter.handle') ?? '' }}">
<meta name="twitter:creator" content="{{ config('services.twitter.handle') ?? '' }}">

<!-- ==================== LINKEDIN ==================== -->
<meta property="og:image:alt" content="{{ $title }}">
<meta name="linkedin:owner" content="{{ config('services.linkedin.handle') ?? '' }}">

<!-- ==================== PINTEREST ==================== -->
<meta name="pinterest-rich-pin" content="true">
<meta property="og:see_also" content="{{ $url }}">
<meta name="pinterest:title" content="{{ $title }}">
<meta name="pinterest:description" content="{{ $description }}">
<meta name="pinterest:image" content="{{ $image }}">

<!-- ==================== TIKTOK ==================== -->
<meta name="tiktok:card" content="summary_large_image">
<meta name="tiktok:title" content="{{ $title }}">
<meta name="tiktok:description" content="{{ $description }}">
<meta name="tiktok:image" content="{{ $image }}">

<!-- ==================== SNAPCHAT ==================== -->
<meta name="snapchat:card" content="summary_large_image">
<meta name="snapchat:title" content="{{ $title }}">
<meta name="snapchat:description" content="{{ $description }}">
<meta name="snapchat:image" content="{{ $image }}">

<!-- ==================== UNIVERSAL MESSAGING APPS (WhatsApp, Discord, Telegram, Slack, etc.) ==================== -->
<meta property="og:image:secure_url" content="{{ $image }}">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- ==================== OPTIONAL ENHANCEMENTS ==================== -->
<meta name="theme-color" content="#ffffff">
<meta name="apple-mobile-web-app-title" content="{{ $title }}">
<meta name="application-name" content="{{ $title }}">
