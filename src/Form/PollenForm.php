<?php

/**
 * @file
 * Contains \Drupal\rnsa_ws\Form\PollenForm
 */

namespace Drupal\rnsa_ws\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\rnsa_ws\RnsaInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Pollen Form
 */
class PollenForm extends FormBase {

  protected $rnsa;

  /**
   * Constructs a new PollenForm
   *
   * @param \Drupal\rnsa_ws\RnsaInterface
   *   The Rnsa object
   */
  public function __construct(RnsaInterface $rnsa) {
    $this->rnsa = $rnsa;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('rnsa')
    );
  }

  /**
  * {@inheritdoc}
  */
  public function getFormId() {
    return 'rnsa_ws_pollen_form';
  }

  /**
  * {@inheritdoc}
  */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['file'] = array(
      '#type' => 'file',
      '#title' => 'Pollen CSV'
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array('#type' => 'submit', '#value' => $this->t('Submit'));

    return $form;
  }

  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $file = file_save_upload(
      'file', array('file_validate_extensions' => array('csv')), FALSE, 0, FILE_EXISTS_REPLACE
    );

    if($file) {
      if($file = file_move($file, 'public://')) {
        $form_state->setStorage(array($file));
      } else {
        $form_state->setError('file', $this->t('Writing permission problem'));
      }
    }
  }

  /**
  * {@inheritdoc}
  */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $csvFile = $form_state->getStorage();
    $fileHandler = fopen(\Drupal::service('file_system')->realpath($csvFile[0]->getFileUri()), 'r');
    $pollenArray = array();

    if($fileHandler) {
      while(($data = fgetcsv($fileHandler, 0, ";")) !== FALSE) {
        $pollenRisk = array(
          reset($data), end($data)
        );
        $pollenArray[] = $pollenRisk
      }
    }

    $result = $this->rnsa->updateRNSARisks($pollenArray, 'rnsa_pollen');
    if($result)
      drupal_set_message($this->t('RNSA pollen successfully updated'), 'status', FALSE);
    else
      drupal_set_message($this->t('An error occured while updating RNSA pollen risks'), 'error', FALSE);
  }
}
