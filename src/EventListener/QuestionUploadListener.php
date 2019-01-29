<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\Question;
use App\Service\FileUploader;

class QuestionUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
      // Retrieve Form as Entity
     $entity = $args->getEntity();

     // This logic only works for Product entities
     if (!$entity instanceof Question) {
         return;
     }

     // Check which fields were changes
     $changes = $args->getEntityChangeSet();

     // Declare a variable that will contain the name of the previous file, if exists.
     $previousFilename = null;

     // Verify if the brochure field was changed
     if(array_key_exists("piecejointe", $changes)){
         // Update previous file name
         $previousFilename = $changes["piecejointe"][0];
     }

     // If no new brochure file was uploaded
     if(is_null($entity->getPieceJointe())){
       if($previousFilename instanceof File){
         $entity->setPieceJointe($previousFilename->getFilename());
       }else{
         $entity->setPieceJointe($previousFilename);
       }
     }else{
       // If some previous file exist
       if((!$previousFilename instanceof File) && (!is_null($previousFilename)) && ($entity->getPiecejointe() instanceof UploadedFile)){
           $pathPreviousFile =  $previousFilename;

           // Remove it
           if(file_exists(__DIR__."/../../public/".$this->uploader->getTargetDirectory().$pathPreviousFile)){
               unlink(__DIR__."/../../public/".$this->uploader->getTargetDirectory().$pathPreviousFile);
           }
       }

       // Upload new file
       $this->uploadFile($entity);
      }
    }


    public function postUpdate(LifecycleEventArgs $args)
    {
      $entity = $args->getEntity();

      if (!$entity instanceof Question) {
          return;
      }

      if ($fileName = $entity->getPiecejointe()) {
        if($fileName != null){
          $entity->setPiecejointe(new File(__DIR__."/../../public/".$this->uploader->getTargetDirectory().$fileName));
        }
      }
    }

    private function uploadFile($entity)
    {
        // upload only works for Product entities
        if (!$entity instanceof Question) {
            return;
        }

        $file = $entity->getPiecejointe();

        // only upload new files
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            $entity->setPiecejointe($fileName);
        }else if ($file instanceof File) {
            $fileName = $file->getFilename();
            $entity->setPiecejointe($fileName);
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Question) {
            return;
        }

        if ($fileName = $entity->getPiecejointe()) {
          if($fileName != null){
            $entity->setPiecejointe(new File(__DIR__."/../../public/".$this->uploader->getTargetDirectory().$fileName));
          }
        }
    }
}
