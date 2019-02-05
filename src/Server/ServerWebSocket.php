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
      case 'DeconnexionServeur':
      $mdp = $messageData->mdp ?? "";
      return $this->DeconnexionServeur($from,$mdp);

      case 'InitPartie':
      $idPartie = $messageData->idPartie ?? 0;
      return $this->InitPartie($from,$idPartie);

      case 'LoginAdmin':
      $mdp = $messageData->mdp ?? '';
      return $this->LoginAdmin($from, $mdp);



      default:
      if($this->gameMode != null){
        $this->gameMode->Action($this,$from,$action,$messageData);
      }
      break;
    }
  }

  public function DeconnexionServeur(ConnectionInterface $from,$mdp)
  {
    if($mdp == "5qsef14qf68qsfe518qs45qs8gf4qg6sr6g"){
      throw new Exception('Deconnexion du serveur');
    }
  }

  public function deconnexion(ConnectionInterface $from)
  {
    if($this->users[$from->resourceId]['status'] == 'Admin'){
      echo "Deconnexion de l'administrateur\n";
      $partie = null;
      $gameMode = null;
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
    if($mdp != "admin"){
      $from->send(json_encode([
        "action" => "LoginAdmin",
        "valide" => false,
        "erreur" => "Mot de passe incorrect"
      ]));
      return false;
    }

    if($this->GetAdmin() != null){
      $from->send(json_encode([
        "action" => "LoginAdmin",
        "valide" => false,
        "erreur" => "Il y a déja un admin de connecté"
      ]));
      return false;
    }

    $from->send(json_encode([
      "action" => "LoginAdmin",
      "valide" => true
    ]));
    $this->users[$from->resourceId]['status'] = 'Admin';


    return true;
  }

  public function RefreshCompteurUser(){
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

  private function GetAdmin()
  {
    foreach ($this->users as $user) {
      if($user['status'] == 'Admin'){
        return $user;
      }
    }
    return null;
  }
}
