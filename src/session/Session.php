<?php

namespace session;

use \Countable;
use \MongoDB;
use session\handler\MongoSessionHandler;

class Session implements Countable {

   protected static $instance = null;
   protected $storage = null;
   protected $mocking = false;

   public static function setMongoHandler(MongoDB $db) {
      $handler = new MongoSessionHandler($db);
      $handler->listen();
   }

   /**
    * @return Session
    */
   public static function singleton() {
      if (self::$instance === null) {
         self::$instance = new self();
      }
      return self::$instance;
   }

   /**
    * Start a session if there is not an ongoing one already.
    * Do not use this constructor directly. 
    * It's meant to be used for testing purposes only!
    */
   public function __construct($storage = null) {
      if (is_null($storage)) {
         if (!session_id()) {
            session_start();
         }
         $this->storage = &$_SESSION; // <<< passed by reference! Arrays are not objects! 
      } else {
         $this->mocking = true;
         $this->storage = $storage;
      }
   }

   /**
    * Get a value from the current session.
    * 
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
   public function get($key, $default = null) {
      if (array_key_exists($key, $this->storage)) {
         return $this->storage[$key];
      }
      return $default;
   }

   /**
    * Get a value and remove it from the current session.
    * 
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
   public function getAndRemove($key, $default = null) {
      if (array_key_exists($key, $this->storage)) {
         $tmp = $this->storage[$key];
         $this->remove($key);
         return $tmp;
      }
      return $default;
   }

   /**
    * Get the whole goddamn thing!
    * 
    * @return array
    */
   public function getAll() {
      return $this->storage;
   }

   /**
    * Add a variable to the current session.
    * 
    * @param string $key
    * @param mixed $value
    */
   public function set($key, $value) {
      $this->storage[$key] = $value;
   }

   /**
    * Check if a variable is in the current session.
    * 
    * @param string $key
    * @return boolean
    */
   public function has($key) {
      return isset($this->storage[$key]);
   }

   /**
    * Remove a variable from the current session.
    * 
    * @param string $key
    */
   public function remove($key) {
      unset($this->storage[$key]);
   }

   /**
    * Remove a variable from the current session.
    * 
    * @param string $key
    */
   public function removeGrep($re) {
      foreach ($this->storage as $key => $value) {
         if (preg_match($re, $key)) {
            unset($this->storage[$key]);
         }
      }
   }

   /**
    * Free the whole damn thing but spare the session memory allocated for the user.
    */
   public function flush() {
      if ($this->mocking === true) {
         $this->storage = array();
      } else {
         session_unset();
      }
   }

   /**
    * Remove the session from memory.
    */
   public function destroy() {
      if ($this->mocking === true) {
         $this->storage = null;
      } else {
         session_destroy();
      }
   }

   /**
    * Return the number of items in memory.
    * 
    * @return integer
    */
   public function count() {
      return count($this->storage);
   }
}