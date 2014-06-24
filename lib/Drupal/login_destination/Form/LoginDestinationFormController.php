<?php
/**
 * @file
 * Contains \Drupal\login_destination\Form\LoginDestinationFormController.
 */

namespace Drupal\login_destination\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Component\Utility\String;

class LoginDestinationFormController extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, array &$form_state) {
    $login_destination = $this->entity;

    $type = $login_destination->getDestinationType();

    $form['name'] = array(
      '#title' => $this->t('Description'),
      '#type' => 'textfield',
      '#default_value' => $login_destination->label(),
      '#description' => $this->t('A short description of this login destination.'),
      '#required' => TRUE,
      '#size' => 30,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $login_destination->id(),
      '#maxlength' => 32,
      '#disabled' => !$login_destination->isNew(),
      '#machine_name' => array(
        'exists' => 'login_destination_load',
        'replace_pattern' => '[^a-z0-9_.]+',
        'source' => array('name'),
        'replace' => '',
      ),
      '#required' => TRUE,
      '#description' => $this->t('A unique machine-readable name for this Login Destination.'),
    );

//      $options = array(
//        LOGIN_DESTINATION_STATIC => $this->t('Internal page or external URL'),
//      );
//
//      $form['destination_type'] = array(
//        '#type' => 'radios',
//        '#title' => 'Redirect to page',
//        '#default_value' => $type,
//        '#options' => $options,
//      );
      $form['destination'] = array(
        '#type' => 'textfield',
        '#default_value' => $login_destination->getDestination(),
        '#description' => $this->t("Specify page by using its path. Example path is %blog for the blog page. %front is the front page. %current is the current page. Precede with http:// for an external URL. Leave empty to redirect to a default page.", array('%blog' => 'blog', '%front' => '<front>', '%current' => '<current>')),
      );

    $form['triggers'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Redirect upon triggers'),
      '#options' => array('login' => 'Login, registration, one-time login link', 'logout' => 'Logout'),
      '#default_value' => !empty($login_destination->triggers) ? $login_destination->triggers : array(),
      '#description' => 'Redirect only upon selected trigger(s). If you select no triggers, all of them will be used.',
    );

    $type = $login_destination->getPagesType();

    $options = array(
      $login_destination::LOGIN_DESTINATION_REDIRECT_NOTLISTED => $this->t('All pages except those listed'),
      $login_destination::LOGIN_DESTINATION_REDIRECT_LISTED => $this->t('Only the listed pages'),
    );
    $description = $this->t("Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page. %login is the login form. %register is the registration form. %reset is the one-time login (e-mail validation).", array('%blog' => 'blog', '%blog-wildcard' => 'blog/*', '%front' => '<front>', '%login' => 'user', '%register' => 'user/register', '%reset' => 'user/*/edit'));

    $form['pages_type'] = array(
      '#type' => 'radios',
      '#title' => $this->t('Redirect from specific pages'),
      '#default_value' => $type,
      '#options' => $options,
    );

    $form['pages'] = array(
      '#type' => 'textarea',
      '#default_value' => $login_destination->getPages(),
      '#description' => $description,
    );

    // @TODO unfuck this
    $default_role_options = array_map('\Drupal\Component\Utility\String::checkPlain', $login_destination->get('roles'));
    if (empty($default_role_options)) {
      $default_role_options = array();
    }

    // @TODO
  //  '#options' => array_map('\Drupal\Component\Utility\String::checkPlain', user_role_names()),

    $role_options = array();
    $roles = user_roles(TRUE);
    foreach ($roles as $role) {
      $role_options[$role->id()] = $role->label();
    }
    // All users who log into the site will be authenticated.
    unset($role_options[DRUPAL_AUTHENTICATED_RID]);

    $form['roles'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Redirect users with roles'),
      '#options' => $role_options,
      '#default_value' => $default_role_options,
      '#description' => 'Redirect only the selected role(s). If you select no roles, all users will be redirected.',
    );

    $form['weight'] = array(
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#default_value' => $login_destination->getWeight(),
      '#description' => $this->t('When evaluating login destination rules, those with lighter (smaller) weights get evaluated before rules with heavier (larger) weights.'),
    );

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, array &$form_state) {
    parent::validate($form, $form_state);

    // Remove empty values from roles and triggers
    $form_state['values']['roles'] = array_filter($form_state['values']['roles']);
    $form_state['values']['triggers'] = array_filter($form_state['values']['triggers']);

  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, array &$form_state) {
    $login_destination = $this->entity;

    $status = $login_destination->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label login destination.', array(
        '%label' => $login_destination->label(),
      )));
    }
    else {
      drupal_set_message($this->t('The %label login destination was not saved.', array(
        '%label' => $login_destination->label(),
      )));
    }

    $form_state['redirect_route']['route_name'] = 'login_destination.list';
  }

}