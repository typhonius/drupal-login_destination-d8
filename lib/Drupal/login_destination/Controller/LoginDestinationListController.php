<?php
/**
 * @file
 * Contains \Drupal\login_destination\Controller\LoginDestinationListController.
 */

namespace Drupal\login_destination\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListController;
use Drupal\Core\Entity\EntityInterface;
use Drupal\login_destination\Entity\LoginDestination;

/**
 * Provides a listing of Foo Bar.
 */
class LoginDestinationListController extends ConfigEntityListController {

  /**
   * Builds the header row for the entity listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildHeader() {
    $header['id'] = $this->t('Name');
    $header['destination'] = $this->t('Destination');
    $header['triggers'] = $this->t('Triggers');
    $header['pages'] = $this->t('Pages');
    $header['roles'] = $this->t('Roles');
    $header['operations'] = $this->t('Operations');

    return $header + parent::buildHeader();
  }

  /**
   * Builds a row for an entity in the entity listing.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity for this row of the list.
   *
   * @return array
   *   A render array structure of fields for this entity.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildRow(EntityInterface $entity) {
    /** @var LoginDestination $entity*/

    $row['id'] = $entity->id();
    $row['destination'] = $entity->viewDestination();
    $row['triggers'] = $entity->viewTriggers();
    $row['pages'] = $entity->viewPages();
    $row['roles'] = $entity->viewRoles();

    return $row + parent::buildRow($entity);
  }

  /**
   * Provides an array of information to build a list of operation links.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity the operations are for.
   *
   * @return array
   *   An associative array of operation link data for this list, keyed by
   *   operation name, containing the following key-value pairs:
   *   - title: The localized title of the operation.
   *   - href: The path for the operation.
   *   - options: An array of URL options for the path.
   *   - weight: The weight of this operation.
   */
  public function getOperations(EntityInterface $entity) {
    $uri = $entity->uri();

    $operations = array();
    if ($entity->access('update')) {
      $operations['edit'] = array(
        'title' => t('Edit'),
        'href' =>  'admin/config/people/login-destination/' . $entity->id() . '/edit',
        'options' => $uri['options'],
        'weight' => 10,
      );
    }
    if ($entity->access('delete')) {
      $operations['delete'] = array(
        'title' => t('Delete'),
        'href' => 'admin/config/people/login-destination/' . $entity->id() . '/delete',
        'options' => $uri['options'],
        'weight' => 100,
      );
    }

    return $operations;
  }
}