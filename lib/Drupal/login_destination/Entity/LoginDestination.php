<?php

/**
 * @file
 * Contains \Drupal\login_destination\FooBar.
 */

namespace Drupal\login_destination\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\login_destination\LoginDestinationInterface;

/**
 *  @ConfigEntityType(
 *   id = "login_destination",
 *   label = @Translation("Login Destination"),
 *   controllers = {
 *     "list_builder" = "Drupal\login_destination\Controller\LoginDestinationListController",
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
 *     "edit-form" = "login_destination.edit",
 *     "delete-form" = "login_destination.delete",
 *   }
 * )
 */
class LoginDestination extends ConfigEntityBase implements LoginDestinationInterface {

  const LOGIN_DESTINATION_REDIRECT_NOTLISTED = 0;

  const LOGIN_DESTINATION_REDIRECT_LISTED = 1;

  /**
   * The Login Destination machine name.
   *
   * @var string
   */
  public $name;

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
  public $pages_type = self::LOGIN_DESTINATION_REDIRECT_NOTLISTED;

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
   * View triggers option.
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|string
   */
  public function viewTriggers(){
    return $this->renderItemList($this->triggers, t('All triggers'));
  }

  /**
   * View roles list.
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|string
   */
  public function viewRoles(){
    return $this->renderItemList($this->roles, t('All roles'));
  }

  /**
   * View pages
   * @return bool|\Drupal\Component\Utility\mixte|FALSE|mixed|string
   */
  public function viewPages(){
    $type = $this->pages_type;

    $pages = trim($this->pages);

    if (empty($pages)) {
      if ($type == self::LOGIN_DESTINATION_REDIRECT_NOTLISTED) {
        return t('All pages');
      }
      else {
        return t('No pages');
      }
    }

    $pages = explode("\n", preg_replace('/\r/', '', check_plain($this->pages)));

    $items = array();
    foreach ($pages as &$page) {
      if ($type == self::LOGIN_DESTINATION_REDIRECT_NOTLISTED) {
        $items[] = "~ " . $page;
      }
      else{
        $items[] = $page;
      }
    }

    $list = array(
      '#type' => 'list',
      '#options' => $items,
    );

    return drupal_render($list);
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
    foreach($array as $value){
      if (!empty($value)) {
        $items[] = check_plain($value);
      }
    }

    if(empty($items))
      return $empty_message;
    else{
      $item_list = array(
        '#theme' => 'item_list',
        '#items' => $items,
        '#list_type' => 'ul',
      );
      return (drupal_render($item_list));
    }
  }
}