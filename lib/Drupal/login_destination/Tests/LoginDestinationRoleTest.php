<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 25/6/14
 * Time: 1:13
 */

namespace Drupal\login_destination\Tests;


class LoginDestinationRoleTest extends LoginDestinationTestBase {

  public static function getInfo() {
    return array(
      'name' => 'Login destination role test',
      'description' => 'Tests that users are redirected only if their roles match',
      'group' => 'Login Destination',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Ensure all authenticated users get redirected.
   */
  protected function testLoginDestinationAllRoles() {
    $this->drupalLogin($this->authUser);
    $expected = url('node', array('absolute' => TRUE));
    $this->assertEqual($this->getUrl(), $expected, 'Redirected to the correct URL on login.');
  }

  /**
   * Ensure users with special roles get redirected correctly and that users without do not.
   */
  protected function testLoginDestinationSpecificRoles() {
    $this->drupalLogin($this->roleUser);
    $expected = url('filter/tips/plain_text', array('absolute' => TRUE));
    $nowurl = $this->getUrl();
    // on node 1 should be on plain_text
    debug("now on $nowurl although should be on $expected");
    $this->assertEqual($this->getUrl(), $expected, 'Redirected to the correct URL on login.');
  }
}