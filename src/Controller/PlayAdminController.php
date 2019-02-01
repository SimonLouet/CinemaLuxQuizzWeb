<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Ratchet\Server\IoServer;
use App\Server\Chat;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

use App\Entity\Partie;
use App\Entity\Question;
use App\Entity\ReponsePossible;

use App\Form\PartieType;
use App\Form\PartieModifierType;


class PlayAdminController extends AbstractController
{
  /**
  * @Route("/play/admin", name="play_admin")
  */

  public function Start($id){
    $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
    return $this->render('play_admin/Play.html.twig', array('id' => $id,'partie' => $partie));
  }




    public function WebSocket($messages = 10, KernelInterface $kernel)
    {
      $application = new Application($kernel);
      $application->setAutoExit(false);

      $input = new ArrayInput([
         'command' => 'app:websocketserver'
      ]);

      // You can use NullOutput() if you don't need the output
      $output = new BufferedOutput();
      $application->run($input, $output);

      // return the output, don't use if you used NullOutput()
      $content = $output->fetch();

      // return new Response(""), if you used NullOutput()
      return new Response($content);
    }

}
