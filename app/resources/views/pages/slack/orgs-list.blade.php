@extends('layout.html')

@section('title', 'Slack organizations')

@section('content')

<div class="container">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <h2 class="template-title-example text-center">Slack organizations</h2>
        </div>
    </div>

    <div class="row margin-top-50">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <p>
                In order to add a Slack Organisation, please create and copy below your <a href="https://api.slack.com/custom-integrations/legacy-tokens" target="_blank">Slack token</a>.
            </p>

            <form action="/slack/orgs" method="POST">
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

                <div class="form-group">
                    <!-- <label for="new-org-token">Token</label> -->
                    <input id="new-org-token" name="token" type="text" class="form-control" placeholder="xoxp-00000000000-00000000000-00000000000-0000000000" required />
                </div>
                <input type="submit" value="Add organization" class="text-center btn btn-primary btn-lg" />
            </form>
        </div>
    </div>

    <div class="row margin-top-50">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <ul class="list-unstyled">
                @foreach ($orgs as $org)
                <li class="slack-org-item">
                    <img src="{{ $org->icon }}" />
                    <strong>{{ $org->name }}</strong>
                    <span>({{ $org->domain }}.slack.com)</span>

                    <a class="pull-right" href="/slack/orgs/{{ $org->id}}/remove"><i class="fa fa-trash"></i></a>
                    <a class="pull-right" href="/slack/orgs/{{ $org->id}}/channels"><i class="fa fa-cog"></i></a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

    <hr/>

    <div class="row margin-top-50">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
            <p>
                For your own security, don't forget to <a href="https://api.slack.com/custom-integrations/legacy-tokens" target="_blank">review active tokens</a> in the Slack API console.
                <br/><br/>
                Privacy does matter, mostly for corporate things! 😘
            </p>
        </div>
    </div>

</div>

@endsection
