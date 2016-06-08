<?php

/**
 * @file
 * Contains \Drupal\rnsa_ws\Controller\RnsaController.
 */

namespace Drupal\rnsa_ws\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\rnsa_ws\RnsaInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class RnsaController extends ControllerBase {

  protected $rnsa;

  /**
   * Constructs a new RnsaController
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

  public function getRNSARiskAction(Request $request) {

    $response = array();

    if(!$request->query->has('department')) {
      $status = JsonResponse::HTTP_BAD_REQUEST;
      $response['error'] = 'Parameter "department" missing';
    } else {
      $department = $request->query->get('department');
      if(!($department >= 1 && $department <= 95)) {
        $status = JsonResponse::HTTP_BAD_REQUEST;
        $response['error'] = 'Parameter "department" incorrect : it should be between 1 and 95';
      } else {
        $risk = $this->rnsa->getRNSARisk($department);
        if(!$risk) {
          $status = JsonResponse::HTTP_NOT_FOUND;
          $response['error'] = 'No result for this department';
        } else {
          $status = JsonResponse::HTTP_OK;
          $response['data']['pollen'] = (int)$risk->pollen;
        }
      }
    }

    return new JsonResponse($response, $status);
  }
}
