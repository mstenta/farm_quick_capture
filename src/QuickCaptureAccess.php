<?php

declare(strict_types=1);

namespace Drupal\farm_quick_capture;

use Drupal\user\RoleInterface;

/**
 * Grant access Quick Capture features.
 */
class QuickCaptureAccess {

  /**
   * Add permissions to managed farmOS roles.
   *
   * @param \Drupal\user\RoleInterface $role
   *   The role to add permissions to.
   *
   * @return array
   *   An array of permission strings.
   */
  public function permissions(RoleInterface $role) {
    $perms = [];

    // Allow farm_worker role to use the quick capture form.
    if ($role->id() == 'farm_worker') {
      $perms[] = 'use farm_quick_capture';
    }

    return $perms;
  }

}
