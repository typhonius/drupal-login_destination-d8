<?php

/**
 * @file
 * Contains \Drupal\login_destination\Form\LoginDestinationSettingsForm.
 */

namespace Drupal\login_destination\Form;

use Drupal\Core\Form\ConfigFormBase;

class LoginDestinationSettingsForm extends ConfigFormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'login_destination_settings';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory->get('login_destination.settings');
    $form['settings']['login_destination_preserve_destination'] = array(
      '#type' => 'checkbox',
      '#default_value' => $config->get('login_destination_preserve_destination'),
      '#title' => t('Preserve the destination parameter'),
      '#description' => t("The 'destination' GET parameter will have priority over the settings of this module. With this setting enabled, redirect from the user login block will not work."),
    );
    $form['settings']['login_destination_immediate_redirect'] = array(
      '#type' => 'checkbox',
      '#default_value' => $config->get('login_destination_immediate_redirect'),
      '#title' => t('Redirect immediately after using one-time login link'),
      '#description' => t("User will be redirected before given the possibility to change their password."),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   An associative array containing the current state of the form.
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->configFactory->get('login_destination.settings')
      ->set('login_destination_preserve_destination', $form_state['values']['login_destination_preserve_destination'])
      ->set('login_destination_immediate_redirect', $form_state['values']['login_destination_immediate_redirect'])
      ->save();

    parent::submitForm($form, $form_state);
  }
}