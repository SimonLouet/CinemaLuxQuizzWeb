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
      case 'Connexion':
      return $this->Connexion($sv,$from);
      break;


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


  private function Connexion($sv,ConnectionInterface $from)
  {
    if($this->partie == null){
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => false,
        "erreur" => "Aucune partie est en cours."
      ]));
      return;
    }

    if($this->etape != "QRCode" && $this->etape != "Presentation"){
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => false,
        "erreur" => "les inscriptions sont fermé."
      ]));
      return;
    }

    $utilisateur = $this->em->getRepository(Utilisateur::class)->findOneBy(['mail' => "MakeyMakey@cinemalux.org" ]);


    if($utilisateur != null){
      echo "Connexion du client\n";
      $valide = true;
      foreach ($this->users as $user) {
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
        return;
      }

      $this->users[$from->resourceId]['status'] = 'Connected';
      $this->users[$from->resourceId]['utilisateur'] = $utilisateur;
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => true,
        "partie" => [
          "id" => $this->partie->getId(),
          "nom" => $this->partie->getNom(),
          "description" => $this->partie->getDescription(),
          "imagefondname" => $this->partie->getimagefondname(),
          "theme" => $this->partie->getTheme(),
          "colortext" => $this->partie->getColortext(),
          "colortitre" => $this->partie->getColortitre(),
          "colorfenetre" => $this->partie->getcolorfenetre(),
          "fontpolice" => $this->partie->getfontpolice(),
          "fontsize" => $this->partie->getfontsize(),
          "modejeux" => $this->partie->getModejeux()
        ]
      ]));
      $from->send(json_encode([
        "action" => "AfficherPresentation"
      ]));
      $this->RefreshCompteurUser();
    }else{
      echo "inscription du client\n";

      $utilisateur = new Utilisateur();

      $utilisateur->setlogin("Equipe");
      $utilisateur->setMdp(md5("5684q6rqg54q6rg4q6gqry5f4"));
      $utilisateur->setMail("MakeyMakey@cinemalux.org");

      $entityManager = $this->em->getManager();
      $entityManager->persist($utilisateur);
      $entityManager->flush();

      $this->users[$from->resourceId]['status'] = 'Connected';
      $this->users[$from->resourceId]['utilisateur'] = $utilisateur;
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => true,
        "partie" => [
          "id" => $this->partie->getId(),
          "nom" => $this->partie->getNom(),
          "description" => $this->partie->getDescription(),
          "imagefondname" => $this->partie->getimagefondname(),
          "theme" => $this->partie->getTheme(),
          "colortext" => $this->partie->getColortext(),
          "colortitre" => $this->partie->getColortitre(),
          "colorfenetre" => $this->partie->getcolorfenetre(),
          "fontpolice" => $this->partie->getfontpolice(),
          "fontsize" => $this->partie->getfontsize(),
          "modejeux" => $this->partie->getModejeux()
        ]
      ]));
      $from->send(json_encode([
        "action" => "AfficherPresentation"
      ]));
      $this->RefreshCompteurUser();
    }
    return true;
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
        $sv->users[$from->resourceId]['repondu'] = microtime(true);
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
