<?php

/**
 * @file
 * Contains \Drupal\rnsa_ws\Rnsa
 */

namespace Drupal\rnsa_ws;

class Rnsa implements RnsaInterface {

  /**
   * {@inheritdoc}
   */
  public function updateRNSARisks($rnsaArray, $rnsaTable) {

    $connection = \Drupal::database();
    for($i = 1; $i <= count($rnsaArray); $i++) {
      $query = $connection->merge($rnsaTable)
        ->key(array('department' => (int)$rnsaArray[$i][0]))
        ->insertFields(array(
          'department' => (int)$rnsaArray[$i][0],
          'risk' => (int)$rnsaArray[$i][1]
        ))
        ->updateFields(array(
          'risk' => (int)$rnsaArray[$i][1]
        ));
      $result = $query->execute();
      if(!in_array($result, array(1,2))) {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getRNSARisk($department) {

    $connection = \Drupal::database();
    $query = $connection->select('rnsa_pollen', 'p');
    $query->addField('p', 'risk', 'pollen');
    $query->condition('p.department', $department, '=');
    $result = $query->execute()
      ->fetchObject();

    return $result;
  }
}
