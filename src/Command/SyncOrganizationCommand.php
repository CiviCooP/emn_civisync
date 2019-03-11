<?php
/**
 * Copyright (c) 2019.  Klaas Eikelboom (klaas.eikelboom@civicoop.org)
 */

namespace Drupal\emn_civisync\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Drupal\Console\Core\Command\ContainerAwareCommand;

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
  private $updater;


  public function __construct($civi,$updater) {
    parent::__construct();
    $this->civi=$civi;
    $this->updater=$updater;
  }

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('civisync:orgn')
      ->addArgument('contact_id',InputArgument::OPTIONAL,'proces only one contact_id, goed for testing')
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
    $orgns = $this->civi->memberlist();

    $queue = \Drupal::queue('sync_contact');
    $queue->createQueue();

    foreach($orgns as $orgn){
      $this->updater->update($orgn);
      $queue->createItem($orgn);
      // $this->getIo()->info("{$orgn['contact_id']} - {$orgn['organization_name']}");
    }
    foreach($this->updater->messages() as $message){
      $this->getIo()->warning($message);
    }
  }
}
