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

      case 'Connexion':
      return $this->Connexion($sv,$from);
      break;

      case 'NextEtape':

      $origin = $messageData->origin ?? "Admin";
      return $this->NextEtape($sv,$from,$origin);
      break;

      case 'RepondreQuestion':
      $idreponse = $messageData->idreponse ?? 0;
      return $this->RepondreQuestion($sv,$from, $idreponse);
      break;

      case 'LoginUser':
      $pseudonyme = $messageData->pseudonyme ?? '';
      $mail = $messageData->mail ?? '';
      $mdp = $messageData->mdp ?? '';
      return $this->LoginUser($sv,$from,$pseudonyme, $mail,$mdp);


      default:
      break;
    }
  }



  private function Connexion($sv,ConnectionInterface $from)
  {
    $from->send(json_encode([
      "action" => "AfficherMenuLogin"
    ]));
    return true;
  }

  private function NextEtape($sv,ConnectionInterface $from,$origin)
  {
    if($sv->GetAutorisation($from)){
      $from = $sv->GetAdmin()['connection'];
      if($sv->etape == "QRCode" || $sv->etape == "Reponse"){
        if($origin == "Admin"){
          $this->nbQuestion += 1;
          if($this->nbQuestion <= count($sv->partie->getQuestions())){
            $this->SendAfficherAttenteQuestion($sv,$from,$this->nbQuestion);
          }else{
            $this->SendAfficherFin($sv,$from);
          }
        }
      }else if($sv->etape == "AttenteQuestion"){
        if($origin == "Admin"){
          $this->SendAfficherQuestion($sv,$from,$this->nbQuestion);
        }
      }else if($sv->etape == "Question"){
        if($origin == "Chrono"){
          $this->SendAfficherReponse($sv,$from,$this->nbQuestion);
        }
      }
    }
    return true;
  }


  private function RepondreQuestion($sv,$from, $idreponse){
    if($sv->users[$from->resourceId]['utilisateur'] != null){
      echo "Utilisateur : ".$sv->users[$from->resourceId]['utilisateur']->getLogin()  ."Reponse : ".$idreponse."\n";
      if($this->question != null && (!$sv->users[$from->resourceId]['repondu']) && $idreponse < count ($this->question->getReponsespossible()) && $sv->etape == "Question"){
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
    $reponsepossibles = array();
    $reponsepossiblevote = array();
    foreach ($this->question->getReponsespossible() as $reponsePossible) {
      $nbvote = $sv->em->getRepository(Reponse::class)->CountReponse($reponsePossible->getId());
      if($reponsePossible->getPiecejointe() != null){
        $piecejointe = $reponsePossible->getPiecejointe()->getFilename();
      }else{
        $piecejointe = null;
      }
      array_push($reponsepossibles, [
        "libelle" => $reponsePossible->getLibelle(),
        "fontsize" => $reponsePossible->getFontSize(),
        "piecejointe" => $piecejointe
      ]);

      array_push($reponsepossiblevote, intval($nbvote[0]["pourcent"]));

    }

    $usersTimer = $sv->em->getRepository(Utilisateur::class)->FirstReponse($this->question->getId());

    $from->send(json_encode([
      "action" => "AfficherReponse",
      "reponsepossible" => $reponsepossibles,
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
      if($reponsePossible->getPiecejointe() != null){
        $piecejointe = $reponsePossible->getPiecejointe()->getFilename();
      }else{
        $piecejointe = null;
      }
      array_push($reponsePossibles, [
        "libelle" => $reponsePossible->getLibelle(),
        "fontsize" => $reponsePossible->getFontSize(),
        "piecejointe" => $piecejointe
      ]);
    }
    if($this->question->getPiecejointe() != null){
      $piecejointe = $this->question->getPiecejointe()->getFilename();
    }else{
      $piecejointe = null;
    }
    $from->send(json_encode([
      "action" => "AfficherQuestion",
      "question" => [
        "id" => $this->question->getId(),
        "numero" => $this->question->getNumero(),
        "libelle" =>$this->question->getLibelle(),
        "piecejointe" => $piecejointe,
        "videoyoutube" => $this->question->getVideoyoutube(),
        "timer" => $this->question->getTimer(),
        "fontsize" => $this->question->getFontsize(),
        "cadeau" => $this->question->getCadeau()
      ],
      "reponsepossible" => $reponsePossibles
    ]));



    $sv->SendAll(json_encode([
      "action" => "AfficherQuestion",
      "question" => [
        "timer" => $this->question->getTimer()
      ],
      "reponsepossible" => $reponsePossibles
    ]));

    $sv->etape = "Question";

    return true;
  }

  private function SendAfficherAttenteQuestion($sv,ConnectionInterface $from, $idQuestion)
  {
    $this->question = $sv->em->getRepository(Question::class)->findOneBy(['partie' => $sv->partie,'numero' => $idQuestion ]);
    if($this->question->getPiecejointe() != null){
      $piecejointe = $this->question->getPiecejointe()->getFilename();
    }else{
      $piecejointe = null;
    }

    $reponsePossibles = array();
    foreach ($this->question->getReponsespossible() as $reponsePossible) {
      array_push($reponsePossibles, [
        "libelle" => $reponsePossible->getLibelle(),
        "fontsize" => $reponsePossible->getFontSize(),
        "correct" => $reponsePossible->getCorrect(),
      ]);
    }

    $from->send(json_encode([
      "action" => "AfficherAttenteQuestion",
      "question" => [
        "id" => $this->question->getId(),
        "numero" => $this->question->getNumero(),
        "libelle" =>$this->question->getLibelle(),
        "piecejointe" => $piecejointe,
        "videoyoutube" => $this->question->getVideoyoutube(),
        "timer" => $this->question->getTimer(),
        "fontsize" => $this->question->getFontsize(),
        "cadeau" => $this->question->getCadeau()
      ]
    ]));


    $sv->SendTelecommande(json_encode([
      "action" => "AfficherQuestionTelecommande",
      "question" => [
        "id" => $this->question->getId(),
        "numero" => $this->question->getNumero(),
        "libelle" =>$this->question->getLibelle(),
        "timer" => $this->question->getTimer(),
        "cadeau" => $this->question->getCadeau()
      ],
      "reponsepossible" => $reponsePossibles
    ]));


    $sv->SendAll(json_encode([
      "action" => "AfficherAttenteQuestion",
      "question" => [
        "numero" => $this->question->getNumero()
      ]
    ]));

    $sv->etape = "AttenteQuestion";

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

  private function LoginUser($sv,ConnectionInterface $from,$pseudonyme, $mail, $mdp)
  {

    echo "Pseudo : ".$pseudonyme  ."  Mail : ".$mail."  Mdp : ".$mdp."\n";
    if($sv->partie == null){
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => false,
        "erreur" => "Aucune partie est en cours."
      ]));
      echo "Aucune partie est en cours.\n";
      return;
    }
    if ($mail == "telecommande@cinemalux.org"){
      if ($mdp != "jesuisunetelecommande"){
        $from->send(json_encode([
          "action" => "LoginUser",
          "valide" => false,
          "erreur" => "Mot de passe incorrect pour la telecommande"
        ]));
        echo "Mot de passe incorrect pour la telecommande\n";
        return;
      }
      $sv->users[$from->resourceId]['status'] = 'Telecommande';
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => true,
        "partie" => [
          "id" => $sv->partie->getId(),
          "nom" => $sv->partie->getNom(),
          "description" => $sv->partie->getDescription(),
          "imagefondname" => $sv->partie->getimagefondname(),
          "theme" => $sv->partie->getTheme(),
          "colortext" => $sv->partie->getColortext(),
          "colortitre" => $sv->partie->getColortitre(),
          "colorfenetre" => $sv->partie->getcolorfenetre(),
          "fontpolice" => $sv->partie->getfontpolice(),
          "fontsize" => $sv->partie->getfontsize(),
          "modejeux" => $sv->partie->getModejeux()
        ]
      ]));
      $from->send(json_encode([
        "action" => "AfficherTelecommande"
      ]));

      return;
    }
    if($sv->etape != "QRCode" && $sv->etape != "Presentation"){
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => false,
        "erreur" => "Les inscriptions sont fermé."
      ]));
      echo "les inscriptions sont fermé.\n";
      return;
    }

    $utilisateur = $sv->em->getRepository(Utilisateur::class)->findOneBy(['mail' => $mail ]);


    if($utilisateur != null){
      echo "Connexion du client\n";
      $valide = true;
      foreach ($sv->users as $user) {
        if($user['status'] == 'Connected'){
          if($utilisateur->getid() == $user['utilisateur']->getid() ){
            $valide = false;
            break;
          }
        }
      }
      if(!$valide){
        $from->send(json_encode([
          "action" => "LoginUser",
          "valide" => false,
          "erreur" => "Compte déjà connecté."
        ]));
        echo "Compte déjà connecté.\n";
        return;
      }

      if(md5($mdp) != $utilisateur->getmdp()){
        $from->send(json_encode([
          "action" => "LoginUser",
          "valide" => false,
          "erreur" => "Mot de passe incorrect."
        ]));
        echo "Mot de passe incorrect.\n";
        return;
      }
    }else{

      echo "inscription du client\n";
      $utilisateur = $sv->em->getRepository(Utilisateur::class)->findOneBy(['login' => $pseudonyme ]);
      if($utilisateur != null){
        $from->send(json_encode([
          "action" => "LoginUser",
          "valide" => false,
          "erreur" => "Pseudo déja utilisé !"
        ]));
        echo "Pseudo déja utilisé !\n";
        return;

      }

      $utilisateur = new Utilisateur();

      $utilisateur->setlogin($pseudonyme);
      $utilisateur->setMdp(md5($mdp));
      $utilisateur->setMail($mail);

      $entityManager = $sv->em->getManager();
      $entityManager->persist($utilisateur);
      $entityManager->flush();
    }
    $utilisateur->addParty($sv->partie);

    $entityManager = $sv->em->getManager();
    $entityManager->persist($utilisateur);
    $entityManager->flush();

    $sv->users[$from->resourceId]['status'] = 'Connected';
    $sv->users[$from->resourceId]['utilisateur'] = $utilisateur;
    $from->send(json_encode([
      "action" => "LoginUser",
      "valide" => true,
      "partie" => [
        "id" => $sv->partie->getId(),
        "nom" => $sv->partie->getNom(),
        "description" => $sv->partie->getDescription(),
        "imagefondname" => $sv->partie->getimagefondname(),
        "theme" => $sv->partie->getTheme(),
        "colortext" => $sv->partie->getColortext(),
        "colortitre" => $sv->partie->getColortitre(),
        "colorfenetre" => $sv->partie->getcolorfenetre(),
        "fontpolice" => $sv->partie->getfontpolice(),
        "fontsize" => $sv->partie->getfontsize(),
        "modejeux" => $sv->partie->getModejeux()
      ]
    ]));
    $from->send(json_encode([
      "action" => "AfficherPresentation"
    ]));
    $sv->RefreshCompteurUser();
    return true;
  }

}
