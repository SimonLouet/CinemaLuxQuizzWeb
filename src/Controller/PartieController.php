<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Partie;
use App\Entity\Question;

use App\Form\PartieType;
use App\Form\PartieModifierType;

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
			$partie = $form->getData();

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($partie);
			$entityManager->flush();
	   
			return $this->render('partie/consulter.html.twig', ['partie' => $partie,]);
		}
		else
		{
			return $this->render('Partie/Ajouter.html.twig', array('form' => $form->createView(),));
		}
	}
	
	public function Lister(){
		$repository = $this->getDoctrine()->getRepository(Partie::class);
		$parties = $repository->findAll();
		return $this->render('Partie/lister.html.twig', [
            'pParties' => $parties,]);	
			
	}
	
	public function Consulter($id){
		
		$partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);
		$questions = $this->getDoctrine()->getRepository(question::class)->findByPartie($partie);
		
		
		if (!$partie) {
			throw $this->createNotFoundException(
            'Aucune partie trouvé avec le numéro '.$id
			);
		}
		
		return $this->render('Partie/Consulter.html.twig', ['partie' => $partie,'questions' => $questions]);
	}
	
	public function Modifier($id, Request $request){

		$partie = $this->getDoctrine()->getRepository(Partie::class)->find($id);

		if (!$partie) {
			throw $this->createNotFoundException('Aucune partie trouvé avec le numéro '.$id);
		}
		else
		{
            $form = $this->createForm(PartieModifierType::class, $partie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                 $partie = $form->getData();
                 $entityManager = $this->getDoctrine()->getManager();
                 $entityManager->persist($partie);
                 $entityManager->flush();
				 
                 return $this->redirect( $this->generateUrl('PartieConsulter', ['id' => $partie->getid()]));
           }
           else{
                return $this->render('partie/ajouter.html.twig', array('form' => $form->createView(),));
           }
        }
 }
}
