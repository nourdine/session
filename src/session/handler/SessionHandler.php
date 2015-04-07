<?php

namespace session\handler;

abstract class SessionHandler {

   public function listen() {

      ini_set('session.save_handler', 'user');

      session_set_save_handler(
         array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc')
      );
   }

   /**
    * Executed when the function session_start() is invoked in the client side.
    */
   abstract public function open($savePath, $sessionName);

   /**
    * Executed just after the session data has been written.
    */
   abstract public function close();

   /**
    * Executed when $_SESSION is accessed in the client side.
    */
   abstract public function read($sessionId);

   /**
    * Executed at the end of the script or when session_write_close() is invoked in the client side.
    */
   abstract public function write($sessionId, $data);

   /**
    * Executed when the function session_destroy() is invoked in the client code.
    */
   abstract public function destroy($sessionId);

   abstract public function gc($maxLifeTime);
}