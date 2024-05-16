<?php namespace Pensoft\Cropper;

use Backend;
use System\Classes\PluginBase;
use Pensoft\Cropper\Widgets\Cropper as CropperWidget;
use Pensoft\Cropper\Models\Settings;
use Pensoft\Cropper\Models\Cropper as CropperModel;
use October\Rain\Resize\Resizer;
use System\Models\File;

/**
 * Cropper Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Cropper',
            'description' => 'No description provided yet...',
            'author'      => 'Pensoft',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        if (Settings::get('is_enable')) {
            $this->imageCropper();
        }
    }

    public function imageCropper()
    {
        \Event::listen('backend.page.beforeDisplay', function($controller, $action, $params) {
            $manager = new CropperWidget($controller);
            $manager->bindToController();
        });
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'crop_image' => [$this, 'crop_image'],
            ],
        ]; 
    }
    
    public function crop_image($data)
    {
        if (!$data) return null;
    
        // Ensure $data is an instance of File
        if (is_string($data)) {
            $data = File::find($data);
        }
    
        if (!$data || !$data instanceof File) {
            return null;
        }
    
        $source_file = $data->getLocalPath();
        $dest_file = 'storage/app/' . dirname($data->getDiskPath()) . '/cropped_' . basename($source_file);
    
        $cropper_model = CropperModel::wherePath($data->id)->first();
    
        // Return original URL if no cropping data is available or if coordinates are null
        if (!$cropper_model || !$cropper_model->data) {
            return url($data->getPath());
        }
    
        $co_ordinates = $cropper_model->data;
    
        if (!isset($co_ordinates['x']) || !isset($co_ordinates['y']) || !isset($co_ordinates['width']) || !isset($co_ordinates['height'])) {
            return url($data->getPath());
        }
    
        $x = $co_ordinates['x'];
        $y = $co_ordinates['y'];
        $width = $co_ordinates['width'];
        $height = $co_ordinates['height'];
    
        // Perform cropping and save the cropped image
        Resizer::open($source_file)
            ->crop($x, $y, $width, $height)
            ->save($dest_file);
    
        return url($dest_file);
    }
    
    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Pensoft\Cropper\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate

        return [
            'pensoft.cropper.some_permission' => [
                'tab' => 'Cropper',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate

        return [
            'cropper' => [
                'label'       => 'Cropper',
                'url'         => Backend::url('pensoft/cropper/mycontroller'),
                'icon'        => 'icon-leaf',
                'permissions' => ['pensoft.cropper.*'],
                'order'       => 500,
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'image_cropper' => [
                'label'       => 'Cropper',
                'description' => 'Cropper Tool Configuration',
                'category'    => 'Cropper',
                'icon'        => 'icon-crop',
                'class'       => 'Pensoft\Cropper\Models\Settings',
                'order'       => 500,
                'keywords'    => 'Image Crop',
                // 'permissions' => ['pensoft.cropper.cropper']
            ]
        ];
    }
}
