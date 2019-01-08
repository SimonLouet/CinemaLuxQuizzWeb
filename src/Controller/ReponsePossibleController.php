<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Question;
use App\Entity\ReponsePossible;

use App\Form\ReponsePossibleType;
use App\Form\ReponsePossibleModifierType;

class ReponsePossibleController extends AbstractController
{
    /**
     * @Route("/reponse/possible", name="reponse_possible")
     */
    public function Ajouter($id,Request $request)
    {
		$question = $this->getDoctrine()->getRepository(Question::class)->find($id);
        $reponsepossible = new ReponsePossible();
		$reponsepossible->setquestion($question);
		
		$form = $this->createForm(ReponsePossibleType::class, $reponsepossible);
		
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$reponsepossible = $form->getData();

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($reponsepossible);
			$entityManager->flush();
	   
			return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $question->getPartie()->getid()]));
		}
		else
		{
			return $this->render('Reponse_Possible/Ajouter.html.twig', array('form' => $form->createView(),));
		}
	}
	
	public function Supprimer($id,Request $request)
    {
		$reponsepossible = $this->getDoctrine()->getRepository(ReponsePossible::class)->find($id);
		
		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->remove($reponsepossible);
		$entityManager->flush();
	   
		return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $reponsepossible->getquestion()->getPartie()->getid()]));
		
	}
	
	public function Modifier($id, Request $request){

		$reponsepossible = $this->getDoctrine()->getRepository(ReponsePossible::class)->find($id);

		if (!$reponsepossible) {
			throw $this->createNotFoundException('Aucune réponse trouvé avec le numéro '.$id);
		}
		else
		{
            $form = $this->createForm(ReponsePossibleModifierType::class, $reponsepossible);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $reponsepossible = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($reponsepossible);
                $entityManager->flush();
				 
                return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $reponsepossible->getquestion()->getpartie()->getid()]));
           }
           else{
                return $this->render('Reponse_Possible/Modifier.html.twig', array('form' => $form->createView(),));
           }
        }
	}
}
