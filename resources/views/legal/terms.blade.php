@extends('legal.layout')

@section('title', 'Terms of Service')

@section('content')
    <h1>Terms of Service</h1>
    <p>{{ config('app.name') }} ({{ config('app.url') }}) is a social media scheduling tool operated by GSD Tech Solutions. By using it you agree to these terms.</p>

    <h2>The service</h2>
    <p>You can connect social accounts you own or manage, schedule and publish posts to them, view analytics for them and, for Instagram, set up automated replies to comments. We publish only the content you create or approve, to the accounts you connect.</p>

    <h2>Your responsibilities</h2>
    <p>You must have the right to the accounts you connect and the content you publish, and follow the terms and community guidelines of each platform, including TikTok, Instagram and Facebook. Do not use the service for spam, unsolicited messages or unlawful content.</p>

    <h2>Third-party platforms</h2>
    <p>Publishing depends on each platform's API. Platforms may reject, limit or remove content, and you can disconnect a channel or revoke our access from the platform at any time.</p>

    <h2>Data</h2>
    <p>How we handle your data is described in the <a href="{{ route('legal.privacy') }}">privacy policy</a>. You can request deletion as described on the <a href="{{ route('legal.data-deletion') }}">data deletion page</a>.</p>

    <h2>Availability and liability</h2>
    <p>The service is provided as is, without warranty. We are not liable for posts that fail to publish, platform outages or decisions platforms make about your content or accounts. We may change these terms or suspend accounts that break them.</p>
@endsection
