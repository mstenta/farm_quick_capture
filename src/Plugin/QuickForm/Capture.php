<?php

declare(strict_types=1);

namespace Drupal\farm_quick_capture\Plugin\QuickForm;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\farm_quick\Attribute\QuickForm;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormBase;
use Drupal\farm_quick\Plugin\QuickForm\QuickFormInterface;
use Drupal\file\FileInterface;

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

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
    AccountInterface $current_user,
    protected FileSystemInterface $fileSystem,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $current_user);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload files'),
      '#upload_validators' => [
        'FileExtension' => [
          'extensions' => implode(' ', array_merge($this->allowedAudioFileExtensions(), $this->allowedImageFileExtensions())),
        ],
      ],
      '#multiple' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Process uploaded files.
    if (!empty($form_state->getValue('upload')) && is_array($form_state->getValue('upload'))) {
      /** @var \Symfony\Component\HttpFoundation\File\UploadedFile[] $uploads */
      $uploads = $form_state->getValue('upload');

      // Prepare the private://capture/[date] directory.
      $directory = 'private://capture/' . date('Y-m-d');
      $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);

      // Process each file.
      $files = [];
      foreach ($uploads as $delta => $upload) {

        // Save the uploaded file.
        $validators = ['FileExtension' => ['extensions' => implode(' ', array_merge($this->allowedAudioFileExtensions(), $this->allowedImageFileExtensions()))]];
        $file = file_save_upload('upload', $validators, $directory, $delta, FileExists::Rename);

        // Show an error if upload failed.
        if (!($file instanceof FileInterface)) {
          return;
        }

        // Make the file permanent.
        $file->setPermanent();
        $file->save();

        // Add the file to the array.
        $files[] = $file;
      }

      // Show a message.
      $this->messenger()->addMessage($this->formatPlural(count($files), 'File uploaded.', 'Files uploaded.'));
    }

    // Otherwise, show a warning.
    else {
      $this->messenger()->addWarning($this->t('No files uploaded.'));
    }
  }

  /**
   * Define allowed audio file extensions.
   */
  protected function allowedAudioFileExtensions() {
    return [
      'mp3',
      'ogg',
      'wav',
    ];
  }

  /**
   * Define allowed image file extensions.
   */
  protected function allowedImageFileExtensions() {
    return [
      'gif',
      'jpeg',
      'jpg',
      'png',
      'webp',
    ];
  }

}
