<?php

namespace session\handler;

use \MongoDB;
use \MongoCursorException;
use session\handler\SessionHandler as SH;

class MongoSessionHandler extends SH {

   /**
    * @var \MongoDB
    */
   private $database;

   /**
    * @var \MongoCollection
    */
   private $collection;

   public function __construct(MongoDB $db) {
      $this->database = $db;
   }

   public function open($savePath, $sessionName) {
      $this->collection = $this->database->selectCollection($sessionName);
      return true;
   }

   /**
    * Called after SessionHandler::write.
    * 
    * @return boolean
    */
   public function close() {
      return true;
   }

   public function read($sessionId) {
      $query = array("session-id" => $sessionId);
      $session = $this->collection->findOne($query);
      if ($session === null) {
         return "";
      }
      return $session["data"];
   }

   /**
    * This writes to memory. 
    * After returning PHP will invoke SessionHandler::close.
    * 
    * @param string $sessionId
    * @param string $data Serialized shit
    * @return boolean
    */
   public function write($sessionId, $data) {
      $query = array(
         "session-id" => $sessionId
      );
      $toSave = array_merge($query, array(
         "data" => $data,
         "time" => time()
      ));
      try {
         $el = $this->collection->findOne($query);
         if ($el === null) {
            $result = $this->collection->save($toSave);
         } else {
            $result = $this->collection->update($query, $toSave);
         }
         return $result["ok"] == 1;
      } catch (MongoCursorException $ex) {
         return false;
      }
   }

   public function destroy($sessionId) {
      $query = array("session-id" => $sessionId);
      try {
         $result = $this->collection->remove($query);
         return $result["ok"] == 1;
      } catch (MongoCursorException $ex) {
         return false;
      }
   }

   public function gc($maxLifeTime) {
      $query = array(
         'time' => array('$lt' => time() - $maxLifeTime)
      );
      try {
         $this->collection->remove($query);
         return true;
      } catch (MongoCursorException $ex) {
         return false;
      }
   }
}