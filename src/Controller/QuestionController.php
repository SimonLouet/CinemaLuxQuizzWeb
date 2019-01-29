<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Partie;
use App\Entity\Question;

use App\Form\QuestionType;
use App\Form\QuestionModifierType;

use App\Service\FileUploader;

class QuestionController extends AbstractController
{
  /**
  * @Route("/question", name="question")
  */
  public function Ajouter($id,Request $request, FileUploader $fileUploader)
  {
    $partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
    $questions = $this->getDoctrine()->getRepository(question::class)->findBypartie($partie);
    $nbQuestion = count($questions);

    $question = new Question();
    $question->setpartie($partie);
    $question->setouverte(false);
    $question->setnumero($nbQuestion + 1);

    $form = $this->createForm(QuestionType::class, $question);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $question = $form->getData();

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($question);
      $entityManager->flush();

      return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $partie->getid()]));
    }
    else
    {
      return $this->render('Question/Ajouter.html.twig', array('form' => $form->createView(),));
    }
  }


  public function Supprimer($id,Request $request)
  {
    $question = $this->getDoctrine()->getRepository(Question::class)->find($id);

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($question);
    $entityManager->flush();

    return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $question->getPartie()->getid()]));

  }

  public function Modifier($id, Request $request){

    $question = $this->getDoctrine()->getRepository(Question::class)->find($id);
    if (!$question) {
      throw $this->createNotFoundException('Aucune question trouvé avec le numéro '.$id);
    }
    else
    {
      $form = $this->createForm(QuestionModifierType::class, $question);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $question = $form->getData();


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($question);
        $entityManager->flush();

        return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $question->getpartie()->getid()]));
      }
      else{
        return $this->render('question/modifier.html.twig', array('form' => $form->createView(),));
      }
    }
  }

  public function Up($id){

    $question = $this->getDoctrine()->getRepository(question::class)->find($id);
    if($question->getnumero() != 1){
      $question->setnumero($question->getnumero() - 1);
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($question);

      $questionnum = $this->getDoctrine()->getRepository(question::class)->findOneBy(['partie' => $question->getpartie(),'numero' => $question->getnumero() ]);
      if($questionnum){
        $questionnum->setnumero($questionnum->getnumero() + 1);
        $entityManager->persist($questionnum);
      }
      $entityManager->flush();

    }
    return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $question->getPartie()->getid()]));
  }


  public function Down($id){

    $question = $this->getDoctrine()->getRepository(question::class)->find($id);
    $questions = $this->getDoctrine()->getRepository(question::class)->findBypartie($question->getpartie());
    $nbQuestion = count($questions);

    if($question->getnumero() != $nbQuestion){
      $question->setnumero($question->getnumero() + 1);
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($question);

      $questionnum = $this->getDoctrine()->getRepository(question::class)->findOneBy(['partie' => $question->getpartie(),'numero' => $question->getnumero() ]);
      if($questionnum){
        $questionnum->setnumero($questionnum->getnumero() - 1);
        $entityManager->persist($questionnum);
      }
      $entityManager->flush();

    }
    return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $question->getPartie()->getid()]));
  }
}
