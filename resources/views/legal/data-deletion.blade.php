@extends('legal.layout')

@section('title', 'Data Deletion')

@section('content')
    <h1>Data Deletion Instructions</h1>
    <p>To delete the data {{ config('app.name') }} holds about you:</p>
    <ol>
        <li>Sign in, open Settings → Channels and disconnect any connected social account. Its tokens are removed immediately.</li>
        <li>To delete everything, delete your workspace from Settings, or email the address below from your account email and we will delete your account and all its data within 30 days.</li>
        <li>You can also remove the app from your Instagram or Facebook settings under Apps and Websites.</li>
    </ol>
@endsection
