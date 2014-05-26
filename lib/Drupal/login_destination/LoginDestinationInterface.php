<?php

/**
 * @file
 * Contains \Drupal\login_destination\LoginDestinationInterface.
 */

namespace Drupal\login_destination;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface LoginDestinationInterface extends ConfigEntityInterface {

  public function viewTriggers();

  public function viewRoles();

  public function viewPages();

  public function viewDestination();
}