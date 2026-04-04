<?php

return [
  "UserChannel" => [
    "class" => App\WebSockets\Realtime\Channels\UserChannel::class,
    "auth" => false,
  ],
  "privatechannel" => [
    "class" => App\WebSockets\Realtime\Channels\PrivateChannel::class,
    "auth" => true,
  ],
];
