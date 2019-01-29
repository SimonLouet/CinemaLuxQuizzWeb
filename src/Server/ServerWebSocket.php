<?php
namespace App\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Doctrine\ORM\EntityManager;

use App\Entity\Partie;
use App\Entity\Utilisateur;

use App\Server\ModeTourParTour;

class ServerWebSocket implements MessageComponentInterface
{
  public $partie = null;
  public $gameMode = null;
  public $users = [];
  public $em;
  public $etape = '';


  public function __construct($em)
  {
    $this->em = $em;
    $this->clients = new \SplObjectStorage();
  }

  public function onOpen(ConnectionInterface $conn)
  {
    $this->users[$conn->resourceId] = [
      'connection' => $conn,
      'utilisateur' => null,
      'status' => 'NotConnected',
      'repondu' => false
    ];
  }

  public function onClose(ConnectionInterface $closedConnection)
  {
    $this->deconnexion($closedConnection);
  }



  public function onError(ConnectionInterface $conn, \Exception $e)
  {
    $this->deconnexion($conn);
  }






  public function onMessage(ConnectionInterface $from, $message)
  {
    $messageData = json_decode($message);
    if ($messageData == null) {
      return false;
    }
    $action = $messageData->action ?? 'unknown';
    echo $action."\n";
    switch ($action) {
      case 'InitPartie':
      $idPartie = $messageData->idPartie ?? 0;
      return $this->InitPartie($from,$idPartie);

      case 'LoginAdmin':
      $mdp = $messageData->mdp ?? '';
      return $this->LoginAdmin($from, $mdp);

      case 'LoginUser':
      $mail = $messageData->mail ?? '';
      $mdp = $messageData->mdp ?? '';
      return $this->LoginUser($from, $mail,$mdp);

      case 'FirstConnexion':
      $pseudonyme = $messageData->pseudonyme ?? '';
      $mail = $messageData->mail ?? '';
      $mdp = $messageData->mdp ?? '';
      return $this->FirstConnexion($from, $mail, $pseudonyme,$mdp);

      default:
      if($this->gameMode != null){
        $this->gameMode->Action($this,$from,$action,$messageData);
      }
      break;
    }
  }

  public function deconnexion(ConnectionInterface $from)
  {
    if($this->users[$from->resourceId]['status'] == 'Admin'){
      echo "Deconnexion de l'administrateur\n";
      $partie = null;
      foreach ($this->users as $user) {
        if($user['status'] == 'Connected'){
          $this->deconnexion($user['connection']);
        }
      }
    }else{
      echo "Deconnexion d'un client\n";
    }

    $from->close();
    unset($this->users[$from->resourceId]);
  }

  private function InitPartie(ConnectionInterface $from, $idPartie)
  {
    if($this->GetAutorisation($from)){
      $entityManager = $this->em->getManager();
      $entityManager->clear();
      $this->em->getRepository(Partie::class)->resetPartie($idPartie);
      $this->partie = $this->em->getRepository(Partie::class)->findOneById($idPartie);

      switch ($this->partie->getModejeux()) {
        case 'TourParTour':
        $this->gameMode = new ModeTourParTour();
        break;

        case 'MakeyMakey':
        $this->gameMode = new ModeMakeyMakey();
        break;

        default:
        $this->gameMode = new ModeTourParTour();
      }


      echo "Initialisation de la partie\n";
      $from->send(json_encode([
        "action" => "InitPartie",
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
      $this->etape = "QRCode";
      $from->send(json_encode([
        "action" => "AfficherQRCode"
      ]));
    }
    return true;
  }



  private function LoginAdmin(ConnectionInterface $from, $mdp)
  {
    if($mdp == "admin"){
      $from->send(json_encode([
        "action" => "LoginAdmin",
        "valide" => true
      ]));
      $this->users[$from->resourceId]['status'] = 'Admin';

    }else{
      $from->send(json_encode([
        "action" => "LoginAdmin",
        "valide" => false,
        "erreur" => "Mot de passe incorrect"
      ]));
    }

    return true;
  }

  private function RefreshCompteurUser(){
    $nb = 0;
    $admin;
    foreach ($this->users as $user) {
      if($user['status'] == 'Connected'){
        $nb += 1;
      }else if($user['status'] == 'Admin'){
        $admin = $user['connection'];
      }
    }
    $admin->send(json_encode([
      "action" => "RefreshCompteurUser",
      "nb" => $nb
    ]));
  }

  private function LoginUser(ConnectionInterface $from, $mail, $mdp)
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

    $utilisateur = $this->em->getRepository(Utilisateur::class)->findOneBy(['mail' => $mail ]);


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

      if(md5($mdp) != $utilisateur->getmdp()){
        $from->send(json_encode([
          "action" => "LoginUser",
          "valide" => false,
          "erreur" => "Mot de passe incorrect."
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

      $utilisateur->setlogin("");
      $utilisateur->setMdp(md5($mdp));
      $utilisateur->setMail($mail);

      $entityManager = $this->em->getManager();
      $entityManager->persist($utilisateur);
      $entityManager->flush();

      $this->users[$from->resourceId]['status'] = 'Connected';
      $this->users[$from->resourceId]['utilisateur'] = $utilisateur;

      $from->send(json_encode([
        "action" => "AfficherFirstConnexion",
        "mail" => $mail
      ]));
      $this->RefreshCompteurUser();
    }
    return true;
  }

  private function FirstConnexion(ConnectionInterface $from, $mail, $pseudonyme,$mdp)
  {
    $this->users[$from->resourceId]['utilisateur']->setlogin($pseudonyme);
    $this->users[$from->resourceId]['utilisateur']->setMail($mail);
    $this->users[$from->resourceId]['utilisateur']->setMdp(md5($mdp));

    $entityManager = $this->em->getManager();
    $entityManager->persist($this->users[$from->resourceId]['utilisateur']);
    $entityManager->flush();
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
    return true;
  }


  public function GetAutorisation(ConnectionInterface $from)
  {
    if($this->users[$from->resourceId]['status'] == 'Admin'){
      return true;
    }
    $from->send(json_encode([
      'action' => 'Erreur',
      'erreur' => 'Access denied'
    ]));
    return false;
  }

}
