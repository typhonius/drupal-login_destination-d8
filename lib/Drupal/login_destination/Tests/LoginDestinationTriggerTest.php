<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 25/6/14
 * Time: 1:13
 */

namespace Drupal\login_destination\Tests;


class LoginDestinationTriggerTest extends LoginDestinationTestBase {

  public static function getInfo() {
    return array(
      'name' => 'Login destination trigger test',
      'description' => 'Tests that users are redirected only if the login destination trigger matches.',
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
   * Ensure a login destination rule happens only on a trigger.
   */
  protected function testLoginDestinationLogoutTrigger() {
    $this->drupalLogin($this->authUser);
    $this->drupalGet('user/logout');
    $expected = url('filter/tips', array('absolute' => TRUE));
    $this->assertEqual($this->getUrl(), $expected, 'Redirected to the correct URL on login.');
  }

}