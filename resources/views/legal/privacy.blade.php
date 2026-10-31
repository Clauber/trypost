@extends('legal.layout')

@section('title', 'Privacy Policy')

@section('content')
    <h1>Privacy Policy</h1>
    <p>{{ config('app.name') }} ({{ config('app.url') }}) is a social media scheduling tool operated by GSD Tech Solutions. This policy explains what we collect when you connect social accounts and how it is used.</p>

    <h2>What we collect</h2>
    <p>Your account email and name. For each social account you connect (Instagram, Facebook, Threads, TikTok and others): the account ID, username, display name, profile picture and the access tokens the platform issues. The content and media you schedule. For Instagram comment automations: the comment ID, the commenter's username and the delivery status of each automated reply.</p>

    <h2>How we use it</h2>
    <p>Only to provide the features you use: publishing and scheduling your posts, showing analytics for your own accounts, and, when you create a comment automation, sending the direct message and optional public reply you configured to people who comment your keyword. We do not sell data, use it for advertising, or share it with third parties other than the platforms you publish to.</p>

    <h2>Retention</h2>
    <p>Data is kept while your account or the connected channel exists. Disconnecting a channel revokes its use; deleting your workspace deletes its data, and we delete your account on request.</p>

    <h2>Your rights</h2>
    <p>You can disconnect channels at any time and request deletion of all your data as described on the <a href="{{ route('legal.data-deletion') }}">data deletion page</a>.</p>
@endsection
