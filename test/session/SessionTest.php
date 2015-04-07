<?php

use session\Session;

class SessionTest extends PHPUnit_Framework_TestCase {

   private $session = null;

   public function setUp() {
      $this->session = new Session(array());
   }

   public function tearDown() {
      $this->session = null; // this makes Sexion::__destruct be called!
   }

   public function testSet() {
      $this->assertEquals(0, count($this->session));
      $this->session->set("p", "v");
      $this->assertEquals(1, count($this->session));
   }

   public function testHas() {
      $this->assertEquals(0, count($this->session));
      $this->session->set("p", "v");
      $this->assertTrue($this->session->has("p"));
      $this->assertEquals(1, count($this->session));
   }

   public function testGet() {
      $this->assertEquals(0, count($this->session));
      $this->session->set("p", "v");
      $this->assertEquals("v", $this->session->get("p"));
      $this->assertEquals(1, count($this->session));
   }

   public function testGetAndRemove() {
      $this->assertEquals(0, count($this->session));
      $this->session->set("p", "v");
      $this->assertEquals("v", $this->session->getAndRemove("p"));
      $this->assertEquals(0, count($this->session));
   }

   public function testRemove() {
      $this->assertEquals(0, count($this->session));
      $this->session->set("p", "v");
      $this->assertEquals(1, count($this->session));
      $this->session->remove("_p_");
      $this->assertEquals(1, count($this->session));
      $this->session->remove("p");
      $this->assertEquals(0, count($this->session));
   }

   public function testRemoveGrep() {
      $this->session->set("namespace_a_name", "fabs");
      $this->session->set("namespace_b_name", "laurent");
      $this->session->removeGrep('/namespace_a/');
      $this->assertEquals(1, count($this->session));
   }

   public function testFlush() {
      $this->assertEquals(0, count($this->session));
      $this->session->set("p", "v");
      $this->assertEquals(1, count($this->session));
      $this->session->flush();
      $this->assertEquals(0, count($this->session));
   }
}