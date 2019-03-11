<?php
/**
 * Copyright (c) 2019.  Klaas Eikelboom (klaas.eikelboom@civicoop.org)
 */

namespace Drupal\emn_civisync;

use \Drupal\node\Entity\Node;

/**
 * Class OrganisationUpdateService.
 */
class OrganisationUpdateService {

  private $messages = [];

  /**
   * @param \Drupal\node\Entity\Node $node
   * @param $orgn
   */
  private function setNode(Node $node, $orgn){
    $node -> set('title',$orgn['organization_name']);
    $node -> set('field_description',$orgn['description']);
    $node -> set('field_email',$orgn['email']);
    $node -> set('field_telephone_number',$orgn['phone']);
    $node -> set('field_year',$orgn['member_since']);

    $organization_type_tid = $this->lookUpTag($orgn['type_of_organization'],'organization_type');
    if($organization_type_tid) {
      $node->set('field_organization_type',  $organization_type_tid);
    } else {
      $this->messages[] = "\"{$orgn['contact_id']} - {$orgn['organization_name']} unknown type {$orgn['type_of_organization']}";
    }
    $membership_type_tid = $this->lookUpTag($orgn['membership_type'],'membership_type');
    if($membership_type_tid) {
      $node -> set('field_membership_type', $membership_type_tid);
    } else {
      $this->messages[] = "\"{$orgn['contact_id']} - {$orgn['organization_name']} unknown membership type {$orgn['membership_type']}";
    }

    $node -> set('field_address',
      [
        'country_code' => $orgn['country_code'],
        'address_line1' => $orgn['street_address'],
        'locality' => $orgn['city'],
        'postal_code' => $orgn['postal_code'],
      ]);
    $node -> set('field_link',[
       'uri' => $orgn['website'],
       'title' => $orgn['shortened_url']
    ]);

    if(empty($orgn['logo'])){
        $this->messages[] = "{$orgn['contact_id']} - {$orgn['organization_name']} has an empty logo";
    } else {

      $fileId = $this->loadFile($orgn);
      $node->set('field_logo', [
        'target_id' => $fileId,
        'alt' => 'Logo Organisation',
        'title' => $orgn['organization_name'],
      ]);
    }

  }

  /**
   * Download a logo for a organization
   * @param $orgn
   *
   * @return int|string|null
   */
  public function loadFile($orgn){
    $uri = $orgn['logo'];
    $extension = explode('.',$uri);
    $extension = array_pop($extension);
    $data = file_get_contents($uri);
    $file = file_save_data($data, 'public:///civicrmlogos/'.$orgn['contact_id'].'.'.$extension, FILE_EXISTS_REPLACE);
    return $file->id();
  }

  /**
   * Find the tag Id in a vocabulaire
   * @param $tag
   * @param $vocabulaire
   *
   * @return mixed
   */
  public function lookUpTag($tag, $vocabulaire){
    $terms = taxonomy_term_load_multiple_by_name($tag,$vocabulaire);
    $keys = array_keys($terms);
    $tid = reset($keys);
    return $tid;
  }

  /**
   * Update an organization (if not not found create it).
   * @param $orgn
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function update($orgn){
    $contact_id = $orgn['contact_id'];

    $query =  \Drupal::service('entity.query')->get('node');
    $node_ids = $query->condition('type','organization')
      ->condition('field_contact_id',$contact_id)
      ->execute();

    if(empty($node_ids)){
      $node = Node::create(
        ['type'=>'organization',
         'field_contact_id'=>$contact_id
        ]);
      $this->setNode($node,$orgn);
      $node->save();
    } else {
      $node_id = reset($node_ids);
      $node = Node::load($node_id);
      $this->setNode($node,$orgn);
      $node -> save();
    }
  }

  /**
   * @param $contact_id
   *
   * @return string
   */
  public function show($contact_id){
    $query =  \Drupal::service('entity.query')->get('node');
    $node_ids = $query->condition('type','organization')
      ->condition('field_contact_id',$contact_id)
      ->execute();
    if(empty($node_ids)){
      return 'Nothing found';
    } else {
      $node_id = reset($node_ids);
      $node = Node::load($node_id);
      $str = 'Something found';
      return $str;
    }
  }

  /**
   * @return array
   */
  public function messages(){
    return $this->messages;
  }

  /**
   * @param $orgn
   * @param $context
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function batchUpdate($orgn, &$context){
    $message = 'Syncing '.$orgn['organization_name'];
    $this->update($orgn);
    $context['message'] = $message;
    $context['results'][] = $orgn['contact_id'];
   }

  /**
   * @param $success
   * @param $results
   * @param $operations
   */
  public function finished($success, $results, $operations){
    if ($success) {
      drupal_set_message('Processed '.count($results).' organizations');
      foreach($this->messages() as $message){
        drupal_set_message($message);
      }
    }
    else {
      drupal_set_message('Finished with Error');
    }
  }

  /**
   * @param $orgns
   *
   * @return array
   */
  public function batch($orgns){

    $operations = [];
    foreach($orgns as $orgn){
      $operations[] = [[$this,'batchUpdate'], [$orgn]];
    }

    return [
      'title' => t('Synchronizing organisations'),
      'operations' => $operations,
      'finished' =>[$this,'finished'],
    ];
  }

  /**
   * @param $nodeId
   * @param $context
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function deleteNode($nodeId, &$context){
    $message = 'Deleting node '.$nodeId;
    $node = Node::load($nodeId);
    $node->delete();
    $context['message'] = $message;
    $context['results'][] = $nodeId;
  }

  /**
   * @return array
   */
  public function deleteBatch(){
    $query =  \Drupal::service('entity.query')->get('node');
    $nodeIds = $query->condition('type','organization') -> execute();

    $operations = [];
    foreach($nodeIds as $nodeId){
      $operations[] = [[$this,'deleteNode'], [$nodeId]];
    }

    return [
      'title' => t('Deleting organisations'),
      'operations' => $operations,
      'finished' =>[$this,'finished'],
    ];


  }

}
