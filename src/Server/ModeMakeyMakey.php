<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\ReponsePossible;
use App\Entity\Utilisateur;

use App\Server\GameMode;


class ModeMakeyMakey implements GameMode
{
  public $nbQuestion = 0;
  public $question;
  public $reponsePossibles = 0;

  public function __construct(){
  }

  public function Action($sv,ConnectionInterface $from,$action,$messageData){

    switch ($action) {
      case 'NextEtape':
      return $this->NextEtape($sv,$from);
      break;

      case 'RepondreQuestion':
      $idreponse = $messageData->idreponse ?? 0;
      return $this->RepondreQuestion($sv,$from, $idreponse);
      break;

      default:
      break;
    }
  }

  private function NextEtape($sv,ConnectionInterface $from)
  {
    $this->nbQuestion += 1;
    $from = $this->GetAdmin($sv)['connection'];
    if($this->nbQuestion <= count($sv->partie->getQuestions())){
      $this->SendAfficherQuestion($sv,$from,$this->nbQuestion);
    }else{
      $this->SendAfficherFin($sv,$from);
    }

    return true;
  }


  private function RepondreQuestion($sv,$from, $idreponse){
   if($sv->users[$from->resourceId]['repondu'] + 4.000 <= microtime(true) && $idreponse < count ($this->question->getReponsespossible()) && $sv->etape == "Question"){
      $sv->users[$from->resourceId]['repondu'] = microtime(true);
       if($this->question->getReponsespossible()[$idreponse]->getCorrect()){
         $sv->etape = "reponseValide";
        $reponse = new Reponse();

        $reponse->setQuestion($this->question);
        $timeReponse = microtime(true);
        $reponse->setTimereponse($timeReponse);
        $reponse->setUtilisateur($sv->users[$from->resourceId]['utilisateur']);
        $reponse->addReponsedonnee($this->question->getReponsespossible()[$idreponse]);

        $entityManager = $sv->em->getManager();
        $entityManager->persist($reponse);
        $entityManager->flush();
        $this->SendAdmin($sv,json_encode([
          "action" => "AfficherReponse",
          "correct" => true,
          "reponselibelle" => $this->question->getReponsespossible()[$idreponse]->getLibelle(),
          "utilisateurlogin" => $sv->users[$from->resourceId]['utilisateur']->getLogin()
        ]));
        $from->send(json_encode([
          "action" => "AfficherResultat",
          "correct" => true
        ]));

      }else{
        $this->SendAdmin($sv,json_encode([
          "action" => "AfficherReponse",
          "correct" => false,
          "reponselibelle" => $this->question->getReponsespossible()[$idreponse]->getLibelle(),
          "utilisateurlogin" => $sv->users[$from->resourceId]['utilisateur']->getLogin()
        ]));

        $from->send(json_encode([
          "action" => "AfficherResultat",
          "correct" => false
        ]));

        foreach ($sv->users as $user) {
          if($user['status'] == 'Connected'){
            $user['repondu'] = microtime(true) - 5000;
          }
        }


      }
    }
  }



  private function SendAfficherQuestion($sv,ConnectionInterface $from, $idQuestion)
  {
    $this->question = $sv->em->getRepository(Question::class)->findOneBy(['partie' => $sv->partie,'numero' => $idQuestion ]);
    $reponsePossibles = array();
    foreach ($this->question->getReponsespossible() as $reponsePossible) {
      array_push($reponsePossibles, [
        "libelle" => $reponsePossible->getLibelle(),
        "fontsize" => $reponsePossible->getFontSize(),
        "piecejointe" => $reponsePossible->getPiecejointe()
      ]);
    }
    if($this->question->getPiecejointe() != null){
      $from->send(json_encode([
        "action" => "AfficherQuestion",
        "question" => [
          "id" => $this->question->getId(),
          "numero" => $this->question->getNumero(),
          "libelle" =>$this->question->getLibelle(),
          "piecejointe" => $this->question->getPiecejointe()->getFilename(),
          "videoyoutube" => $this->question->getVideoyoutube(),
          "timer" => $this->question->getTimer(),
          "fontsize" => $this->question->getFontsize()
        ],
        "reponsepossible" => $reponsePossibles
      ]));
    }else{
      $from->send(json_encode([
        "action" => "AfficherQuestion",
        "question" => [
          "id" => $this->question->getId(),
          "numero" => $this->question->getNumero(),
          "libelle" =>$this->question->getLibelle(),
          "piecejointe" => null,
          "videoyoutube" => $this->question->getVideoyoutube(),
          "timer" => $this->question->getTimer(),
          "fontsize" => $this->question->getFontsize()
        ],
        "reponsepossible" => $reponsePossibles
      ]));
    }



    $this->SendAll($sv,json_encode([
      "action" => "AfficherQuestion",
      "question" => [
        "timer" => $this->question->getTimer()
      ],
      "reponsepossible" => $reponsePossibles
    ]));

    $sv->etape = "Question";

    return true;
  }

  private function SendAfficherFin($sv,ConnectionInterface $from)
  {

    $scores = $sv->em->getRepository(utilisateur::class)->Score($sv->partie->getid());
    foreach ($sv->users as $user) {

      if($user['status'] == 'Connected'){
        $valide = false;
        foreach ($scores as $score) {
          if($user['utilisateur']->getLogin() == $score["login"]){
            $valide = true;
            $user['connection']->send(json_encode([
              "action" => "AfficherFin",
              "score" => $score["score"]
            ]));
          }
        }

        if(!$valide){
          array_push($scores,["login" => $user['utilisateur']->getLogin(),"score"=>0]);
          $user['connection']->send(json_encode([
            "action" => "AfficherFin",
            "score" => 0
          ]));
        }
      }
    }
    $from->send(json_encode([
      "action" => "AfficherFin",
      "score" => $scores
    ]));

    return true;
  }

  private function SendAll($sv,$json)
  {
    foreach ($sv->users as $user) {
      if($user['status'] == 'Connected'){
        $user['connection']->send($json);
      }
    }
    return true;
  }

  private function SendAdmin($sv,$json)
  {
    foreach ($sv->users as $user) {
      if($user['status'] == 'Admin'){
        $user['connection']->send($json);
        return true;
      }
    }
    return true;
  }

  private function GetAdmin($sv)
  {
    foreach ($sv->users as $user) {
      if($user['status'] == 'Admin'){
        return $user;
      }
    }
    return null;
  }

}
