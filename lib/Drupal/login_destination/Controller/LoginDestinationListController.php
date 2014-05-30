<?php
/**
 * @file
 * Contains \Drupal\login_destination\Controller\LoginDestinationListController.
 */

namespace Drupal\login_destination\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\login_destination\Entity\LoginDestination;

/**
 * Provides a listing of Foo Bar.
 */
class LoginDestinationListController extends ConfigEntityListBuilder {

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

}