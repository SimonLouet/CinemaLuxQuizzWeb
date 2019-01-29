<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\ReponsePossible;
use App\Entity\Utilisateur;

use App\Server\GameMode;


class ModeTourParTour implements GameMode
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
    if($sv->GetAutorisation($from)){
      if($sv->etape == "QRCode" || $sv->etape == "Reponse"){
        $this->nbQuestion += 1;
        if($this->nbQuestion <= count($sv->partie->getQuestions())){
          $this->SendAfficherQuestion($sv,$from,$this->nbQuestion);
        }else{
          $this->SendAfficherFin($sv,$from);
        }


      }else if($sv->etape == "Question"){
        $this->SendAfficherReponse($sv,$from,$this->nbQuestion);
      }
    }
    return true;
  }


  private function RepondreQuestion($sv,$from, $idreponse){
    if((!$sv->users[$from->resourceId]['repondu']) && $idreponse < count ($this->question->getReponsespossible()) && $sv->etape == "Question"){
      $sv->users[$from->resourceId]['repondu'] = true;
      $reponse = new Reponse();

      $reponse->setQuestion($this->question);
      $timeReponse = microtime(true);
      $reponse->setTimereponse($timeReponse);
      $reponse->setUtilisateur($sv->users[$from->resourceId]['utilisateur']);
      $reponse->addReponsedonnee($this->question->getReponsespossible()[$idreponse]);

      $entityManager = $sv->em->getManager();
      $entityManager->persist($reponse);
      $entityManager->flush();
    }
  }




  private function SendAfficherReponse($sv,ConnectionInterface $from,$idQuestion)
  {
    foreach ($sv->users as $user) {
      if($user['status'] == 'Connected'){
        $sv->users[$user['connection']->resourceId]['repondu'] = false;
      }
    }
    $this->question = $sv->em->getRepository(Question::class)->findOneBy(['partie' => $sv->partie,'numero' => $idQuestion ]);
    $this->reponsePossibles = $sv->em->getRepository(ReponsePossible::class)->findByQuestion($this->question);
    $reponsepossible = array();
    $reponsepossiblevote = array();
    foreach ($this->question->getReponsespossible() as $reponsePossible) {
      $nbvote = $sv->em->getRepository(Reponse::class)->CountReponse($reponsePossible->getId());

      array_push($reponsepossible, ['libelle'=>$reponsePossible->getLibelle(),
                                    'fontsize' => $reponsePossible->getFontsize()]);
      array_push($reponsepossiblevote, intval($nbvote[0]["pourcent"]));

    }

    $usersTimer = $sv->em->getRepository(Utilisateur::class)->FirstReponse($this->question->getId());

    $from->send(json_encode([
      "action" => "AfficherReponse",
      "reponsepossible" => $reponsepossible,
      "reponsepossiblevote" => $reponsepossiblevote,
      "usertimer" => $usersTimer
    ]));

    foreach ($sv->users as $user) {
      if($user['status'] == 'Connected'){
        $correct = 0;
        foreach ($usersTimer as $userTimer) {
          if($userTimer["id"] == $user['utilisateur']->getId()){
            $correct = 1;
            $user['connection']->send(json_encode([
              "action" => "AfficherResultat",
              "correct" => $userTimer["correct"]
            ]));
            echo "qzdqdzdqzd \n";
            break;
          }
        }
        if($correct == 0){
          $user['connection']->send(json_encode([
            "action" => "AfficherResultat",
            "correct" => 0
          ]));
        }
      }
    }

    $sv->etape = "Reponse";
    return true;
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

    $from->send(json_encode([
      "action" => "AfficherQuestion",
      "question" => [
        "id" => $this->question->getId(),
        "numero" => $this->question->getNumero(),
        "libelle" =>$this->question->getLibelle(),
        "videoyoutube" => $this->question->getVideoyoutube(),
        "timer" => $this->question->getTimer(),
        "fontsize" => $this->question->getFontsize()
      ],
      "reponsepossible" => $reponsePossibles
    ]));


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
}
