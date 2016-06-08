<?php

/**
 * @file
 * Contains \Drupal\rnsa_ws\RnsaInterface
 */

namespace Drupal\rnsa_ws;

interface RnsaInterface {

  /**
   * Update RNSA's risks
   *
   * @param array $rnsaArray
   *   The data to update
   * @param string $rnsaTable
   *   The table to update
   * @return bool
   *   TRUE if update successfull, or FALSE on failure.
   */
  public function updateRNSARisks($rnsaArray, $rnsaTable);

  /**
   * Get RNSA's risks
   *
   * @param int $department
   *   The department code
   * @return bool
   *   @object if risk found, or FALSE on failure.
   */
  public function getRNSARisk($department);

}
