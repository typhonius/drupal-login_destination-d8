<?php
/**
 * @file
 * Contains \Drupal\login_destination\Form\LoginDestinationFormController.
 */

namespace Drupal\login_destination\Form;

use Drupal\Core\Entity\EntityFormController;
use Drupal\login_destination\Entity\LoginDestination;

class LoginDestinationFormController extends EntityFormController {

  /**
   * Returns the actual form array to be built.
   *
   * @see \Drupal\Core\Entity\EntityFormController::build()
   */
  public function form(array $form, array &$form_state) {
    $form = parent::form($form, $form_state);

    $login_destination = $this->entity; /** @var LoginDestination $login_destination */

    $access = \Drupal::currentUser()->hasPermission('use PHP for settings');

    $type = $login_destination->destination_type;

    $form['name'] = array(
      '#title' => t('Name'),
      '#type' => 'textfield',
      '#default_value' => $login_destination->name,
      '#description' => t('The human-readable name of this Login Destination.'),
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
        'source' => array('name'),
      ),
      '#description' => t('A unique machine-readable name for this Login Destination.'),
    );


    if ($type == LOGIN_DESTINATION_SNIPPET && !$access) {
      $form['destination_type'] = array(
        '#type' => 'value',
        '#value' => LOGIN_DESTINATION_SNIPPET,
      );
      $form['destination'] = array(
        '#type' => 'value',
        '#value' => $login_destination->destination,
      );
    }
    else {
      $options = array(
        LOGIN_DESTINATION_STATIC => t('Internal page or external URL'),
      );
      $description = t("Specify page by using its path. Example path is %blog for the blog page. %front is the front page. %current is the current page. Precede with http:// for an external URL. Leave empty to redirect to a default page.", array('%blog' => 'blog', '%front' => '<front>', '%current' => '<current>'));

      if ($access) {
        $options += array(LOGIN_DESTINATION_SNIPPET => t('Page returned by this PHP code (experts only)'));
        $description .= ' ' . t('If the PHP option is chosen, enter PHP code between %php. It should return either a string value or an array of params that the %function function will understand, e.g. %example. For more information, see the online API entry for <a href="@url">url function</a>. Note that executing incorrect PHP code can break your Drupal site.', array('%php' => '<?php ?>', '%function' => 'url($path = \'\', array $options = array())', '%example' => '<?php return array(\'blog\', array(\'fragment\' => \'overlay=admin/config\', ), ); ?>', '@url' => 'http://api.drupal.org/api/drupal/core--includes--common.inc/function/url/8'));
      }

      $form['destination_type'] = array(
        '#type' => 'radios',
        '#title' => 'Redirect to page',
        '#default_value' => $type,
        '#options' => $options,
      );
      $form['destination'] = array(
        '#type' => 'textarea',
        '#default_value' => $login_destination->destination,
        '#description' => $description,
      );
    }

    $triggers = array_map('check_plain', $login_destination->triggers);
    if (empty($triggers)) {
      $triggers = array();
    }

    $form['triggers'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Redirect upon triggers'),
      '#options' => array('login' => 'Login, registration, one-time login link', 'logout' => 'Logout'),
      '#default_value' => $triggers,
      '#description' => 'Redirect only upon selected trigger(s). If you select no triggers, all of them will be used.',
    );

    $type = $login_destination->pages_type;

    if ($type == LOGIN_DESTINATION_REDIRECT_PHP && !$access) {
      $form['pages_type'] = array(
        '#type' => 'value',
        '#value' => LOGIN_DESTINATION_REDIRECT_PHP,
      );
      $form['pages'] = array(
        '#type' => 'value',
        '#value' => $login_destination->destination,
      );
    }
    else {
      $options = array(
        LOGIN_DESTINATION_REDIRECT_NOTLISTED => t('All pages except those listed'),
        LOGIN_DESTINATION_REDIRECT_LISTED => t('Only the listed pages'),
      );
      $description = t("Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard. Example paths are %blog for the blog page and %blog-wildcard for every personal blog. %front is the front page. %login is the login form. %register is the registration form. %reset is the one-time login (e-mail validation).", array('%blog' => 'blog', '%blog-wildcard' => 'blog/*', '%front' => '<front>', '%login' => 'user', '%register' => 'user/register', '%reset' => 'user/*/edit'));

      if ($access) {
        $options += array(LOGIN_DESTINATION_REDIRECT_PHP => t('Pages on which this PHP code returns <code>TRUE</code> (experts only)'));
        $description .= ' ' . t('If the PHP option is chosen, enter PHP code between %php. Note that executing incorrect PHP code can break your Drupal site.', array('%php' => '<?php ?>'));
      }

      $form['pages_type'] = array(
        '#type' => 'radios',
        '#title' => t('Redirect from specific pages'),
        '#default_value' => $type,
        '#options' => $options,
      );
      $form['pages'] = array(
        '#type' => 'textarea',
        '#default_value' => $login_destination->pages,
        '#description' => $description,
      );
    }

    $default_role_options = array_map('check_plain', $login_destination->roles);
    if (empty($default_role_options)) {
      $default_role_options = array();
    }

    $form['roles'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Redirect users with roles'),
      '#options' => _login_destination_role_options(),
      '#default_value' => $default_role_options,
      '#description' => 'Redirect only the selected role(s). If you select no roles, all users will be redirected.',
    );

    $form['weight'] = array(
      '#type' => 'weight',
      '#title' => t('Weight'),
      '#default_value' => $login_destination->weight,
      '#description' => t('When evaluating login destination rules, those with lighter (smaller) weights get evaluated before rules with heavier (larger) weights.'),
    );

    return $form;
  }

  /**
   * Form submission handler for the 'save' action.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   A reference to a keyed array containing the current state of the form.
   */
  public function save(array $form, array &$form_state) {
    $login_destination = $this->entity; /** @var LoginDestination $login_destination*/

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

    $form_state['redirect'] = 'admin/config/people/login-destination';
  }

  /**
   * Form submission handler for the 'delete' action.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   A reference to a keyed array containing the current state of the form.
   */
  public function delete(array $form, array &$form_state) {
    $destination = array();
    $request = $this->getRequest();
    if ($request->query->has('destination')) {
      $destination = drupal_get_destination();
      $request->query->remove('destination');
    }

    $form_state['redirect'] = array('admin/config/people/login-destination/delete/' . $this->entity->id() , array('query' => $destination));
  }

}