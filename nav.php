<?php $pid = botPID(); ?>
<div class="title">Twitch Chat History</div>
  <nav>
    <div><a class="<?= $uri == '/'         ? 'selected' : '' ?>" href="/"         >home     </a></div>
    <div><a class="<?= $uri == '/query'    ? 'selected' : '' ?>" href="query"     >query    </a></div>
    <div><a class="<?= $uri == '/emotes'   ? 'selected' : '' ?>" href="emotes"    >emotes   </a></div>
    <div><a class="<?= $uri == '/config'   ? 'selected' : '' ?>" href="config"    >config   </a></div>
    <div title="<?=$pid?>" id='status'><a title="connects/disconnects to/from irc" href="/<?=$pid ? 'disconnect' : 'connect' ?>"><?=botPID() > 0 ? 'disconnect' : 'connect' ?></a></div>
  </nav>