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
    return $this->render('play_admin/play.html.twig', array('id' => $id,'partie' => $partie));
  }


}
