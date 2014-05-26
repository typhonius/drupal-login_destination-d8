<?php

/**
 * @file
 * Contains \Drupal\login_destination\FooBar.
 */

namespace Drupal\login_destination\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\login_destination\LoginDestinationInterface;

/**
 *  @EntityType(
 *   id = "login_destination",
 *   label = @Translation("Login Destination"),
 *   controllers = {
 *     "storage" = "Drupal\Core\Config\Entity\ConfigStorageController",
 *     "list" = "Drupal\login_destination\Controller\LoginDestinationListController",
 *     "form" = {
 *       "add" = "Drupal\login_destination\Form\LoginDestinationFormController",
 *       "edit" = "Drupal\login_destination\Form\LoginDestinationFormController",
 *       "delete" = "Drupal\login_destination\Form\LoginDestinationDeleteForm"
 *     }
 *   },
 *   config_prefix = "login_destination",
 *   admin_permission = "administer users",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "admin/config/people/login-destination/edit/{login_destination}"
 *   }
 * )
 */
class LoginDestination extends ConfigEntityBase implements LoginDestinationInterface {

  /**
   * The Login Destination ID.
   *
   * @var string
   */
  public $id;

  /**
   * The Login Destination UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The Login Destination triggers.
   *
   * @var array
   */
  public $triggers = array();

  /**
   * The Login Destination roles.
   *
   * @var array
   */
  public $roles = array();

  /**
   * The Login Destination pages type.
   *
   * @var int
   */
  public $pages_type = LOGIN_DESTINATION_REDIRECT_NOTLISTED;

  /**
   * The Login Destination pages.
   *
   * @var string
   */
  public $pages = '';

  /**
   * The Login Destination destination type.
   *
   * @var int
   */
  public $destination_type = LOGIN_DESTINATION_STATIC;

  /**
   * The Login Destination destination.
   *
   * @var string
   */
  public $destination = '<front>';

  /**
   * The Login Destination weight.
   *
   * @var int
   */
  public $weight = 0;

  /**
   * The Login Destination machine name.
   *
   * @var string
   */
  public $name;

  /**
   * View triggers option.
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|string
   */
  public function viewTriggers(){
    return $this->renderItemList($this->triggers, 'All triggers');
  }

  /**
   * View roles list.
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|string
   */
  public function viewRoles(){
    return $this->renderItemList($this->roles, 'All roles');
  }

  /**
   * View pages
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|mixed|string
   */
  public function viewPages(){
    $type = $this->pages_type;

    if ($type == LOGIN_DESTINATION_REDIRECT_PHP) {
      return nl2br(check_plain($this->pages));
    }

    $pages = trim($this->pages);

    if (empty($pages)) {
      if ($type == LOGIN_DESTINATION_REDIRECT_NOTLISTED) {
        return t('All pages');
      }
      else {
        return t('No pages');
      }
    }

    $pages = explode("\n", preg_replace('/\r/', '', check_plain($this->pages)));

    $items = array();
    foreach ($pages as &$page) {
      if ($type == LOGIN_DESTINATION_REDIRECT_NOTLISTED) {
        $items[] = "~ " . $page;
      }
      else{
        $items[] = $page;
      }
    }

    return theme_item_list(array('items' => $items, 'title' => '', 'list_type' => 'ul', 'attributes' => array(),));
  }

  /**
   * View destination list.
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|mixed|string
   */
  public function viewDestination(){
    $output = nl2br(check_plain($this->destination));

    if (empty($output)) {
      $output = t('Empty');
    }

    return $output;
  }

  /**
   * Render item list.
   * @param $array
   * @param $empty_message
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|string
   */
  protected function renderItemList($array, $empty_message){
    foreach($array as $key => $value){
      if(!empty($value))
        $items[$key] = check_plain($value);
    }

    if(empty($items))
      return t($empty_message);
    else{
      $item_list = array(
        'items' => $items,
        'title' => '',
        'list_type' => 'ul',
        'attributes' => array(),
      );
      return theme_item_list($item_list);
    }
  }
}