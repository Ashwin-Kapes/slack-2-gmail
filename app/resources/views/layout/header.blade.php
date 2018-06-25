
<header>
  @if (Request::is(''))
  <nav class="navbar navbar-default navbar-fixed-top">
    @else ()
    <nav class="navbar navbar-default active">
    @endif
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/" title="Slack 2 Gmail">
          Slack 2 Gmail
        </a>
      </div>

      <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="https://github.com/samber/slack-2-gmail" title="Github" target="_blank">Github</a></li>
          @if (Auth::check())
          <li><p><a href="/slack/orgs" title="Slack organizations" class="btn btn-default navbar-btn">Slack orgs.</a></p></li>
          <li><a href="/logout" title="Logout">Logout</a></li>
          @else
          <li><a href="/login" title="Login">Login</a></li>
          <li><p><a href="/join" title="Join" class="btn btn-default navbar-btn">Join</a></p></li>
          @endif
        </ul>
      </div>
    </div>
  </nav>
</header>
