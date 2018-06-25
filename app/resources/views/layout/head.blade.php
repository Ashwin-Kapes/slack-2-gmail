<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>Slack to Gmail | @yield('title')</title>
<meta name="description" content="Backup Slack channels into Gmail in 1 click.">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('links')
    @if(config('h5b.manifest'))
        <link rel="manifest" href="{{ config('h5b.manifest') }}">
    @endif
    @if(config('h5b.appleicon'))
        <link rel="apple-touch-icon" href="{{ config('h5b.appleicon') }}">
    @endif
    <!-- Place favicon.ico in the root directory -->
@show

@section('styles')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hind+Siliguri:300,600|Martel:400,900">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css">

    <link rel="stylesheet" href="/css/app.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
@show
