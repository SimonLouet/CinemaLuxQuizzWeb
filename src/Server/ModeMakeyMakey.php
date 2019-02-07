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
  public $reponse = 0;
  public $nbreponse = 0;


  public function __construct(){
  }

  public function Action($sv,ConnectionInterface $from,$action,$messageData){

    switch ($action) {
      case 'Connexion':
      return $this->Connexion($sv,$from);
      break;


      case 'NextEtape':
      $origin = $messageData->origin ?? "";
      return $this->NextEtape($sv,$from,$origin);
      break;

      case 'RepondreQuestion':
      $idreponse = $messageData->idreponse ?? 0;
      $equipe = $messageData->equipe ?? 0;
      return $this->RepondreQuestion($sv,$from,$idreponse, $equipe);
      break;

      default:
      break;
    }
  }


  private function Connexion($sv,ConnectionInterface $from)
  {
    if($sv->partie == null){
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => false,
        "erreur" => "Aucune partie est en cours."
      ]));
      return;
    }

    if($sv->etape != "QRCode" && $sv->etape != "Presentation"){
      $from->send(json_encode([
        "action" => "LoginUser",
        "valide" => false,
        "erreur" => "les inscriptions sont fermé."
      ]));
      return;
    }

    $equipe1 = $sv->em->getRepository(Utilisateur::class)->findOneBy(['mail' => "MakeyMakey1@cinemalux.org" ]);
    $equipe2 = $sv->em->getRepository(Utilisateur::class)->findOneBy(['mail' => "MakeyMakey2@cinemalux.org" ]);

    if($equipe1 != null && $equipe2 != null){
      echo "Connexion du client\n";
      $valide = true;
      foreach ($sv->users as $user) {
        if($user['status'] == 'Connected'){
          if($equipe1->getid() == $user['equipe1']->getid() ||  $equipe2->getid() == $user['equipe2']->getid() ){
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
    }else{
      echo "inscription du client\n";

      $equipe1 = new Utilisateur();
      $equipe2 = new Utilisateur();

      $equipe1->setlogin("Equipe1");
      $equipe2->setlogin("Equipe2");
      $equipe1->setMdp(md5("5684q6rqg54q6rg4q6gqry5f4"));
      $equipe2->setMdp(md5("5684q6rqg54q6rg4q6gqry5f4"));
      $equipe1->setMail("MakeyMakey1@cinemalux.org");
      $equipe2->setMail("MakeyMakey2@cinemalux.org");

      $entityManager = $sv->em->getManager();
      $entityManager->persist($equipe1);
      $entityManager->persist($equipe2);
      $entityManager->flush();
    }


    $sv->users[$from->resourceId]['status'] = 'Connected';
    $sv->users[$from->resourceId]['equipe1'] = $equipe1;
    $sv->users[$from->resourceId]['equipe1Timer'] = 0.0;
    $sv->users[$from->resourceId]['equipe2'] = $equipe2;
    $sv->users[$from->resourceId]['equipe2Timer'] = 0.0;
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


  private function NextEtape($sv,ConnectionInterface $from,$origin)
  {

    $from = $this->GetAdmin($sv)['connection'];
    if($sv->etape == "Question" && $origin == "Admin"){
      if($this->reponse < $this->nbreponse){
        $this->SendAfficherReponsePossible($sv,$from);
      }else{
        $sv->etape = "Reponse";
      }
    }else if(($sv->etape == "Reponse"|| $sv->etape == "QRCode") && $origin == "Chrono"){
      $this->nbQuestion += 1;
      if($this->nbQuestion <= count($sv->partie->getQuestions())){
        $this->SendAfficherQuestion($sv,$from,$this->nbQuestion);
      }else{
        $this->SendAfficherFin($sv,$from);
      }
    }
    return true;
  }


  private function RepondreQuestion($sv,$from, $idreponse,$equipe){
   if($idreponse < count ($this->question->getReponsespossible()) && $sv->etape == "Reponse"){
     if($sv->users[$from->resourceId]['equipe'.$equipe.'Timer'] + 4.000 > microtime(true)){
       return;
     }
     if($this->question->getReponsespossible()[$idreponse]->getCorrect()){
        $sv->etape = "reponseValide";
        $reponse = new Reponse();
        $reponse->setQuestion($this->question);
        $timeReponse = microtime(true);
        $reponse->setTimereponse($timeReponse);
        $reponse->setUtilisateur($sv->users[$from->resourceId]['equipe'.$equipe]);
        $reponse->addReponsedonnee($this->question->getReponsespossible()[$idreponse]);
        $entityManager = $sv->em->getManager();
        $entityManager->persist($reponse);
        $entityManager->flush();
        $this->SendAdmin($sv,json_encode([
          "action" => "AfficherReponse",
          "correct" => true,
          "reponselibelle" => $this->question->getReponsespossible()[$idreponse]->getLibelle(),
          "utilisateurlogin" => $sv->users[$from->resourceId]['equipe'.$equipe]->getLogin()
        ]));
      }else{
        $sv->users[$from->resourceId]['equipe'.$equipe.'Timer'] = microtime(true);
        $this->SendAdmin($sv,json_encode([
          "action" => "AfficherReponse",
          "correct" => false,
          "reponselibelle" => $this->question->getReponsespossible()[$idreponse]->getLibelle(),
          "utilisateurlogin" => $sv->users[$from->resourceId]['equipe'.$equipe]->getLogin()
        ]));
      }
    }
  }



  private function SendAfficherQuestion($sv,ConnectionInterface $from, $idQuestion)
  {
    $this->nbreponse = 0;
    $this->reponse = 0;
    $this->question = $sv->em->getRepository(Question::class)->findOneBy(['partie' => $sv->partie,'numero' => $idQuestion ]);
    $reponsePossibles = array();
    foreach ($this->question->getReponsespossible() as $reponsePossible) {
      array_push($reponsePossibles, [
        "libelle" => $reponsePossible->getLibelle(),
        "fontsize" => $reponsePossible->getFontSize(),
        "piecejointe" => $reponsePossible->getPiecejointe()
      ]);
      $this->nbreponse += 1;
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
        $equipe1 = false;
        $equipe2= false;
        foreach ($scores as $score) {
          if($user['equipe1']->getLogin() == $score["login"]){
            $equipe1 = true;
          }

          if($user['equipe2']->getLogin() == $score["login"] ){
            $equipe2 = true;
          }
        }

        if(!$equipe1){
          array_push($scores,["login" => "Equipe1","score"=>0]);
        }
        if(!$equipe2){
          array_push($scores,["login" => "Equipe2","score"=>0]);
        }
      }
    }
    $from->send(json_encode([
      "action" => "AfficherFin",
      "score" => $scores
    ]));

    return true;
  }

  private function SendAfficherReponsePossible($sv,ConnectionInterface $from)
  {
    $this->reponse += 1;
    $from->send(json_encode([
      "action" => "AfficherReponsePossible"
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
