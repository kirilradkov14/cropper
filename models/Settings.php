<?php namespace Pensoft\Cropper\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'pensoft_cropper_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}