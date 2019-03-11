<?php
/**
 * Copyright (c) 2019.  Klaas Eikelboom (klaas.eikelboom@civicoop.org)
 */

namespace Drupal\emn_civisync\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\emn_civisync\OrganisationUpdateService;

/**
 * Class SyncBatchForm.
 */
class SyncBatchForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sync_batch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['info'] = [
      '#type' => 'markup' ,
      '#markup' => 'Download and insert member organizations from the CRM (including logos)'.'<br/>',
    ];

    $form['options'] = [
      '#type' => 'radios',
      '#title' => 'What action do you want to execute',
      '#options' => [
        'D' => 'Delete Members',
        'S' => 'Sync Members',
      ],
      '#default_value' => 'S'
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Execute'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $civicrm = new \Drupal\emn_civisync\CiviCRMService($this->configFactory());
    $updater = new OrganisationUpdateService();
    if($form_state->getValue('options')=='S') {
      batch_set($updater->batch($civicrm->memberlist()));
    } else {
      batch_set($updater->deleteBatch());
    }
  }

}
