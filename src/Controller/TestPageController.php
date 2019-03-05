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

    $orgn_lit = <<< 'JSON'
{
            "contact_id": "1199",
            "organization_name": "Banca Popolare Etica",
            "phone": "0039 49 8771111",
            "email": "schinello@bancaetica.com",
            "website": "http://www.bancaetica.it/",
            "shortened_url": "bancaetica.it",
            "city": "Padova",
            "street_address" : "Dillenburglaan 2",
            "postal_code": "35131",
            "country": "Italy",
            "country_code": "IT",
            "logo": "https://crm.european-microfinance.org/civicrm/contact/imagefile?photo=bancaetica_b33e145c9bae6426d03182c5a7bca6cb.png",
            "description": "Banca Etica is Italy's first ethical bank, with the mission of encouraging socio-economic initiatives in the field of sustainable human and social development. The organisation opened its first branch in Padua in 1999.",
            "member_since": "2009",
            "type_of_organization": "Bank",
            "membership_type": "Corporate"
}
JSON;
    $orgn = json_decode($orgn_lit,TRUE);

    $updater = new OrganisationUpdateService();

    $updater->update($orgn);

    return [
      '#type' => 'markup',
      '#markup' => $updater->show(1199),
    ];
  }

}
