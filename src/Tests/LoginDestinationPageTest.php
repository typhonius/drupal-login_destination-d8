<?php
/**
 * Created by PhpStorm.
 * User: typhonius
 * Date: 25/6/14
 * Time: 1:13
 */

namespace Drupal\login_destination\Tests;


class LoginDestinationPageTest extends LoginDestinationTestBase {

  public static function getInfo() {
    return array(
      'name' => 'Login destination page test',
      'description' => 'Tests that users are redirected only if they are on the correct page.',
      'group' => 'Login Destination',
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Place the user login block prior to attempting to log in on a node page.
    $adminUser = $this->drupalCreateUser(array('administer blocks'));
    $this->drupalLogin($adminUser);
    $this->drupalPlaceBlock('user_login_block');
    $this->drupalLogout($adminUser);
  }

  /**
   * Ensure users only get redirected when logging in on certain pages.
   */
  protected function testLoginDestinationSpecificPage() {
    $user = $this->roleUser;

    // Log in using the user login block.
    $edit = array();
    $edit['name'] = $user->getUsername();
    $edit['pass'] = $user->pass_raw;
    $this->drupalPostForm('node/' . $this->node->id(), $edit, t('Log in'));

    $expected = url('filter/tips/restricted_html', array('absolute' => TRUE));
    $this->assertEqual($this->getUrl(), $expected, 'Redirected to the correct URL on login.');
  }
}