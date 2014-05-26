<?php

/**
 * @file
 * Contains \Drupal\login_destination\Form\LoginDestinationDeleteForm.
 */

namespace Drupal\login_destination\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;

class LoginDestinationDeleteForm extends EntityConfirmFormBase {

  /**
   * Returns the question to ask the user.
   *
   * @return string
   *   The form question. The page title will be set to this value.
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the login destination @destination?', array('@destination' => $this->entity->id()));
  }

  /**
   * Returns the route to go to if the user cancels the action.
   *
   * @return array
   *   An associative array with the following keys:
   *   - route_name: The name of the route.
   *   - route_parameters: (optional) An associative array of parameter names
   *     and values.
   *   - options: (optional) An associative array of additional options. See
   *     \Drupal\Core\Routing\UrlGeneratorInterface::generateFromRoute() for
   *     comprehensive documentation.
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'login_destination.list',
    );
  }

  /**
   * Returns a caption for the button that confirms the action.
   *
   * @return string
   *   The form confirmation text.
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }


  /**
   * Updates the form's entity by processing this submission's values.
   *
   * Note: Before this can be safely invoked the entity form must have passed
   * validation, i.e. only add this as form #submit handler if validation is
   * added as well.
   *
   * @param array $form
   *   A nested array form elements comprising the form.
   * @param array $form_state
   *   An associative array containing the current state of the form.
   *
   * @return \Drupal\Core\Entity\EntityInterface|void
   */
  public function submit(array $form, array &$form_state) {
    $this->entity->delete();
    drupal_set_message(t('The login destination %destination has been deleted.', array('%destination' => $this->entity->id())));
    $form_state['redirect'] = 'admin/config/people/login-destination';
  }
}