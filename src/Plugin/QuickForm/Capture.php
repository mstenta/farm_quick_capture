<?php

declare(strict_types=1);

namespace Drupal\farm_quick_capture\Plugin\QuickForm;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\farm_quick\Attribute\QuickForm;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormBase;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormInterface;

/**
 * Capture quick form.
 */
#[QuickForm(
  id: 'capture',
  label: new TranslatableMarkup('Capture'),
  description: new TranslatableMarkup('Capture photos or audio recordings.'),
  helpText: new TranslatableMarkup('Use this form to capture photos or audio recordings which can later be processed into assets and logs.'),
  permissions: [
    'use farm_quick_capture',
  ],
)]
class Capture extends QuickFormBase implements QuickFormInterface {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
