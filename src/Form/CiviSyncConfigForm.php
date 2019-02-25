<?php
/**
 * Copyright (c) 2019.  Klaas Eikelboom (klaas.eikelboom@civicoop.org)
 */

namespace Drupal\emn_civisync\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CiviSyncConfigForm.
 */
class CiviSyncConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'emn_civisync.civisyncconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'civi_sync_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $civicrm = new \Drupal\emn_civisync\CiviCRMService($this->configFactory());

    $config = $this->config('emn_civisync.civisyncconfig');

    $form['info'] = [
      '#type' => 'markup' ,
      '#markup' => $civicrm->check(),

    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site url'),
      '#description' => $this->t('Url of the site where the CiviCRM organisations are synced from'),
      '#maxlength' => 100,
      '#size' => 64,
      '#default_value' => $config->get('url'),
    ];
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Api Key'),
      '#description' => $this->t('This is a field in the CiviCRM contact that is connected to the drupal user'),
      '#maxlength' => 65,
      '#size' => 40,
      '#default_value' => $config->get('api_key'),
    ];
    $form['key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Site Key'),
      '#description' => $this->t('Look for it in the CiviCRM configuration file'),
      '#maxlength' => 65,
      '#size' => 40,
      '#default_value' => $config->get('key'),
    ];
    return parent::buildForm($form, $form_state);
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
    parent::submitForm($form, $form_state);

    $this->config('emn_civisync.civisyncconfig')
      ->set('url', $form_state->getValue('url'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('key', $form_state->getValue('key'))
      ->save();
  }

}
