<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 25/6/14
 * Time: 1:06
 */

namespace Drupal\login_destination\Tests;


class LoginDestinationWeightTest extends LoginDestinationTestBase {

  public static function getInfo() {
    return array(
      'name' => 'Login destination weight test',
      'description' => 'Tests that rules with a lighter weight are executed before those with heavier weights.',
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
   * Ensure lighter weighted rules are executed before heavier weighted rules.
   */
  protected function testLoginDestinationWeightComparison() {
    $node = $this->node;
    $this->drupalLogin($this->authUser);
    $expected = url('node', array('absolute' => TRUE));
    $this->assertEqual($this->getUrl(), $expected, 'Redirected to the correct URL');
  }
}