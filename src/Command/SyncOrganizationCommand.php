<?php
/**
 * Copyright (c) 2019.  Klaas Eikelboom (klaas.eikelboom@civicoop.org)
 */

namespace Drupal\emn_civisync\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Drupal\Console\Core\Command\Command;
use Drupal\Console\Core\Command\ContainerAwareCommand;
use Drupal\Console\Annotations\DrupalCommand;

/**
 * Class SyncOrganizationCommand.
 *
 * @DrupalCommand (
 *     extension="emn_civisync",
 *     extensionType="module"
 * )
 */
class SyncOrganizationCommand extends ContainerAwareCommand {

  private $civi;

  public function __construct($civi) {
    parent::__construct();
    $this->civi=$civi;
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('civisync:orgn')
      ->setDescription($this->trans('commands.civisync.orgn.description'));
  }

 /**
  * {@inheritdoc}
  */
  protected function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);
    $this->getIo()->info('initialize');
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->getIo()->info('execute');
    $this->getIo()->info($this->civi->check());
  }
}
