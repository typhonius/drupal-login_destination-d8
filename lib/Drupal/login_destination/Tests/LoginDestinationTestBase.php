<?php
/**
 * @file
 * Contains \Drupal\user_restrictions\Tests\LoginDestinationTestBase.
 */

namespace Drupal\login_destination\Tests;

use Drupal\simpletest\WebTestBase;

class LoginDestinationTestBase extends WebTestBase{

  protected $authUser;

  protected $roleUser;

  protected $ld_role;

  protected $node;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('login_destination', 'node', 'block');

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // @TODO create roles
    // @TODO create users
    // @todo create rules

    $this->createResources();
  }

  /**
   *
   */
  protected function createResources() {

    $this->authUser = $this->drupalCreateUser();

    $this->ld_role = $this->drupalCreateRole(array(), 'ld_role_1', 'ld_role_1');
    $this->roleUser = $this->drupalCreateUser();
    $this->roleUser->addRole($this->ld_role);
    $this->roleUser->save();

    // Create a node so we have something on the /node page.
    $this->node = $this->drupalCreateNode(array());

    $rules[] = array(
      'id' => 'priority_weight',
      'name' => 'Priority weight',
      'triggers' => array(),
      'roles' => array(),
      'destination' => 'node',
      'destination_type' => 0,
      'pages' => '',
      'weight' => '-5',
    );

    $rules[] = array(
      'id' => 'non_priority_weight',
      'name' => 'Non-priority weight',
      'triggers' => array(),
      'roles' => array(),
      'destination' => 'user',
      'pages_type' => 0,
      'pages' => '',
      'weight' => '10',
    );

    $rules[] = array(
      'id' => 'role_based_rule',
      'name' => 'Role based login rule',
      'triggers' => array(),
      'roles' => array('ld_role_1' => 'ld_role_1'),
      'destination' => 'filter/tips/plain_text',
      'pages_type' => 0,
      'pages' => '',
      'weight' => '-8',
    );

    $rules[] = array(
      'id' => 'logout_trigger_rule',
      'name' => 'Triggered rule only on logout',
      'triggers' => array('logout' => 'logout'),
      'roles' => array(),
      'destination' => 'filter/tips',
      'pages_type' => 0,
      'pages' => '',
      'weight' => '-8',
    );

    $rules[] = array(
      'id' => 'page_based_login_rule',
      'name' => 'Login on node rule',
      'triggers' => array(),
      'roles' => array(),
      'destination' => 'filter/tips/restricted_html',
      'pages_type' => 1,
      'pages' => 'node/' . $this->node->id(),
      'weight' => '-10',
    );

    foreach ($rules as $rule) {
      $entity = entity_create('login_destination', $rule);
      $entity->save();
    }

  }

}