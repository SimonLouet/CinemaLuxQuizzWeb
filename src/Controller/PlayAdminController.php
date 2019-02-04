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


use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;


class PlayAdminController extends AbstractController
{
  /**
  * @Route("/play/admin", name="play_admin")
  */

  public function Start($id){
    $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);

    $qrCode = new QrCode("http://".$_SERVER['HTTP_HOST'].$this->generateUrl('PartiePlayuserStart'));
    $qrCode->setSize(300);

    // Set advanced options
    $qrCode->setWriterByName('png');
    $qrCode->setMargin(10);
    $qrCode->setEncoding('UTF-8');
    $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH));
    $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
    $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
    $qrCode->setRoundBlockSize(true);
    $qrCode->setValidateResult(false);
    $qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

    // Save it to a file
    $qrCode->writeFile('uploads/QrCode/QRcode.png');

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
