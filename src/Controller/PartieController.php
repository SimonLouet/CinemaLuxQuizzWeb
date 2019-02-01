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

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;

class PartieController extends AbstractController
{
  /**
  * @Route("/partie", name="partie")
  */
  public function Ajouter(Request $request)
  {
    $partie = new Partie();
    $form = $this->createForm(PartieType::class, $partie);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $partie->setImagefondname($this->loadimageFond($form['imagefondname']->getData(),""));

      $partie = $form->getData();

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($partie);
      $entityManager->flush();

      // Create a basic QR code
      $qrCode = new QrCode("http://192.168.24.49".$this->generateUrl('PartiePlayuserStart', ['id' =>$partie->getid()]));
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
      $qrCode->writeFile('uploads/QrCode/'.$partie->getid().'.png');

      return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $partie->getid()]));
    }
    else
    {
      return $this->render('partie/Ajouter.html.twig', array('form' => $form->createView(),));
    }
  }


  public function Cloner($id,Request $request)
  {
    $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
    $partie = clone $partie;
    $partie->setUtilisateurs(null);
    $partie->setNom($partie->getNom()."(clone)");
    echo $partie->getQuestions()[0]->getlibelle();
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($partie);
    $entityManager->flush();
    return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $partie->getid()]));
  }


  public function Lister(){


    $repository = $this->getDoctrine()->getRepository(Partie::class);
    $parties = $repository->findAll();
    return $this->render('partie/Lister.html.twig', [
      'pParties' => $parties,]);

    }

    public function Consulter($id){

      $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
      $questions = $this->getDoctrine()->getRepository(question::class)->findByPartieOrderByNumero($partie);


      if (!$partie) {
        throw $this->createNotFoundException(
          'Aucune partie trouvé avec le numéro '.$id
        );
      }

      return $this->render('partie/Consulter.html.twig', ['partie' => $partie,'questions' => $questions]);
    }

    public function Supprimer($id,Request $request)
    {
      $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($partie);
      $entityManager->flush();

      return $this->redirect( $this->generateUrl('PartieLister'));

    }



    public function Modifier($id, Request $request){

      $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);

      if (!$partie) {
        throw $this->createNotFoundException('Aucune partie trouvé avec le numéro '.$id);
      }
      else
      {
        $fileName = $partie->getImagefondname();
        $partie->setImagefondname(null);
        $form = $this->createForm(PartieModifierType::class, $partie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

          $partie->setImagefondname($this->loadimageFond($form['imagefondname']->getData(),$fileName));

          $partie = $form->getData();

          $entityManager = $this->getDoctrine()->getManager();
          $entityManager->persist($partie);
          $entityManager->flush();

          return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $partie->getid()]));
        }
        else{
          return $this->render('partie/Modifier.html.twig', array('form' => $form->createView(),));
        }
      }
    }


    public function Statistique($id){

      $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
      if (!$partie) {
        throw $this->createNotFoundException(
          'Aucune partie trouvé avec le numéro '.$id
        );
      }
      $questions = $this->getDoctrine()->getRepository(question::class)->findByPartieOrderByNumero($partie);
      $reponseStat = array();
      foreach ($questions as $question) {
        array_push($reponseStat,$this->getDoctrine()->getRepository(Question::class)->ReponseStatistique($question->getId()));
      }


      return $this->render('partie/Statistique.html.twig', ['partie' => $partie,'questions' => $questions,'reponseStat' => $reponseStat]);
    }









    private function loadimageFond($file,$fileName)
    {
      if($file != null || $file != ""){
        $fileName =  $this->generateUniqueFileName().'.'.$file->guessExtension();

        try {
          $file->move('uploads/imageFond',$fileName);
        } catch (FileException $e) {

        }
      }
      return $fileName;
    }


    private function generateUniqueFileName()
    {
      // md5() reduces the similarity of the file names generated by
      // uniqid(), which is based on timestamps
      return md5(uniqid());
    }
  }
