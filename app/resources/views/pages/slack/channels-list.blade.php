@extends('layout.html')

@section('title', $org->name . ' channels')

@section('content')

<div class="container">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <h2 class="template-title-example text-center">{{ $org->name }} channels</h2>
        </div>
    </div>

    <div class="row margin-top-50">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <p>What channels would you like to sync/backup on Gmail ?</p>

            <p>
                <small>
                    We will first sync the existing history.
                </small>
            </p>
        </div>
    </div>

    <div class="row margin-top-50">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <form action="/slack/orgs/{{ $org->id }}/channels" method="POST">
                @method('POST')
                {{ csrf_field() }}

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        </ul>
                    </div>
                @endif

                <ul class="list-unstyled">
                    @foreach ($channels as $channel)
                    <li class="slack-channel-item">
                        <div class="form-group">
                            <input id="channel-select-{{ $channel->id }}" name="channels[]" type="checkbox" value="{{ $channel->id }}" {{ $channel->is_backup_enabled ? 'checked' : '' }} />
                            <label for="channel-select-{{ $channel->id }}">
                                <strong>#{{ $channel->name }}</strong>
                            </label>
                        </div>
                    </li>
                    @endforeach
                </ul>

                <input type="submit" value="Save sync config" class="text-center btn btn-primary btn-lg" />
            </form>
        </div>
    </div>

</div>

@endsection
