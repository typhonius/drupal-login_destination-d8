<?php

/**
 * @file
 * Contains \Drupal\login_destination\LoginDestinationManager.
 */

namespace Drupal\login_destination;

use Drupal\Component\Utility\Unicode;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Path\AliasManager;
use Drupal\Core\Routing\UrlGenerator;
use Drupal\login_destination\Entity\LoginDestination;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Url;

class LoginDestinationManager {

  public $request;

  public $response;

  public $aliasmanager;

  protected $account;

  function __construct(Request $request, AliasManager $aliasmanager) {
    $this->request = $request;
    $this->aliasmanager = $aliasmanager;
  }

  function findDestination($trigger, $account) {
    $this->account = $account;

    // Sort Entities via weight and id (as is done on the list builder).
    $destinations = entity_load_multiple('login_destination');
    uasort($destinations, '\Drupal\login_destination\Entity\LoginDestination::sort');

    $path = current_path();
    $path_alias = Unicode::strtolower($this->aliasmanager->getAliasByPath($path));

    foreach ($destinations as $destination) {

      // Determine if the trigger matches that of the login destination rule.
      $destination_triggers = $destination->get('triggers');
      if (empty($destination_triggers) || in_array($trigger, $destination_triggers)) {
        $destination_roles = $destination->get('roles');
        $role_match = array_intersect($this->account->getRoles(), $destination->get('roles'));

        // Ensure the user logging in has a role allowed by the login destination rule.
        if (empty($destination_roles) || !empty($role_match)) {
          $pages = $destination->get('pages');
          $page_match = drupal_match_path($path_alias, $pages) || (($path != $path_alias) && drupal_match_path($path, $pages));

          // Make sure the page matches (or does not match if the rule specifies that).
          $destination_page_type = $destination->get('pages_type');
          if (($destination_page_type == $destination::LOGIN_DESTINATION_REDIRECT_LISTED && $page_match) ||
            $destination_page_type == $destination::LOGIN_DESTINATION_REDIRECT_NOTLISTED && (!$page_match || empty($pages))) {

            return $destination;
          }
        }
      }
    }
    return FALSE;
  }

  function redirect(LoginDestination $destination) {

    // This is a pretty ratchet redirect job. It may change further in 8.x.
    $url = $destination->getDestination();

    $parsed_url = UrlHelper::parse($url);
    $options = array();
    $options['query'] = $parsed_url['query'];
    $options['fragment'] = $parsed_url['fragment'];
    $options['absolute'] = TRUE;

    $redirect = url($parsed_url['path'], $options);

    $this->response = new RedirectResponse($redirect, RedirectResponse::HTTP_FOUND);
    $this->response->send();
  }

}
