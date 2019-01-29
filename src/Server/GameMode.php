<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

use App\Entity\Partie;

interface GameMode
{
  public function Action($sv,ConnectionInterface $from,$action,$messageData);

}
