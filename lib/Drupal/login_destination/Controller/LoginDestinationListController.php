<?php
/**
 * @file
 * Contains \Drupal\login_destination\Controller\LoginDestinationListController.
 */

namespace Drupal\login_destination\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\login_destination\Entity\LoginDestination;

/**
 * Provides a listing of Login Destinations.
 */
class LoginDestinationListController extends DraggableListBuilder {

  /**
   * The key to use for the form element containing the entities.
   *
   * @var string
   */
  protected $entitiesKey = 'login_destination';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'login_destination_overview';
  }

  /**
   * Builds the header row for the entity listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\Core\Entity\EntityListController::render()
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['roles'] = $this->t('Roles');
    $header['destination'] = $this->t('Destination');
    $header['triggers'] = $this->t('Triggers');
    $header['pages'] = $this->t('Pages');
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

    $roles = $entity->viewRoles();
    $destination = $entity->viewDestination();
    $triggers = $entity->viewTriggers();
    $pages = $entity->viewPages();

    $row['label'] = $entity->label();
    $row['roles'] = !empty($this->weightKey) ? array('#markup' => $roles) : $roles;
    $row['destination'] = !empty($this->weightKey) ? array('#markup' => $destination) : $destination;
    $row['triggers'] = !empty($this->weightKey) ? array('#markup' => $triggers) : $triggers;
    $row['pages'] = !empty($this->weightKey) ? array('#markup' => $pages) : $pages;

    return $row + parent::buildRow($entity);
  }

}