<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Partie;
use App\Entity\Question;
use App\Entity\ReponsePossible;

use App\Form\PartieType;
use App\Form\PartieModifierType;

class PlayUserController extends AbstractController
{
  /**
  * @Route("/play/user", name="play_user")
  */
  public function Start($id){
    $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
    return $this->render('play_user/play.html.twig', array('id' => $id,'partie' => $partie));
  }
}
