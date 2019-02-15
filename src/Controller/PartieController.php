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

use Dompdf\Dompdf;


class PartieController extends AbstractController
{
  /**
  * @Route("/partie", name="partie")
  */

  public function FichePresentateur($id,Request $request)
  {
    $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
    $questions = $this->getDoctrine()->getRepository(question::class)->findByPartieOrderByNumero($partie);

    $rendu = "<h1>".$partie->getNom()."</h1><br/>";
    $i = 1;
    foreach ($questions as $question) {
      $rendu .= "<h3>".$i."-".$question->getLibelle()."</h3>";
      foreach ($question->getreponsespossible() as $reponsepossible) {
        if($reponsepossible->getCorrect()){
          $rendu .="<p style='color: green;'><b> - ".$reponsepossible->getLibelle()."</b></p>";
        }else{
          $rendu .="<p style='color: red;'> - ".$reponsepossible->getLibelle()."</p>";
        }
      }
      $i ++;
    }


    // instantiate and use the dompdf class
    $dompdf = new Dompdf();
    $dompdf->loadHtml($rendu);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream($partie->getNom()."_Fiche_Presentateur.pdf");

    return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $id]));

  }


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
        $stats = $this->getDoctrine()->getRepository(Question::class)->ReponseStatistique($question->getId());
        $i = 0;
        foreach($stats as $stat){
          $date = new \DateTime();
          $date->setTimestamp($stat["timereponse"]);
          $stats[$i]["timereponse"] =  $date->format("Y/m/d H:i:s:u");
          $i += 1;
        }
        array_push($reponseStat,$stats);
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
