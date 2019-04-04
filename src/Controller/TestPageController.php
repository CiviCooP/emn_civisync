<?php

namespace Drupal\emn_civisync\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\emn_civisync\OrganisationUpdateService;
use Drupal\emn_civisync\CiviCRMService;

/**
 * Class TestPageController.
 */
class TestPageController extends ControllerBase {

  /**
   * Showresult.
   *
   * @return string
   *   Return Hello string.
   */
  public function showResult() {

    $query =  \Drupal::service('entity.query')->get('node');
    $organization_name = 'AMIK - Association of Microfinance Institutions of Kosovo';
    $prep = $query->condition('type','organization')
      ->condition('title',$organization_name)
      ->addTag('debug');
    $prep->execute();

    return [
      '#type' => 'markup',
      '#markup' => 'Hi, I am testpage controller',
    ];
  }

}
