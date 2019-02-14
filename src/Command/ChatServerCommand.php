<?php
namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\Server\IoServer;
use App\Server\ServerWebSocket;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Entity\Partie;

$server;

class ChatServerCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:websocketserver')
            ->setDescription('Start chat server');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      try {
        $em = $this->getContainer()->get('doctrine');

        $server = IoServer::factory(
            new HttpServer(new WsServer(new ServerWebSocket($em))),
            8080,
            '0.0.0.0'
        );
        $server->run();
      } catch (RequestException  $e) {
      }
    }



}
