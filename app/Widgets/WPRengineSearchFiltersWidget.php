<?php
namespace WPRengine\Widgets;
use WPRengine\Controllers\FrontController;

class WPRengineSearchFiltersWidget extends \WP_Widget {
    public $frontController;
	public function __construct() {
		parent::__construct(
			'wp-rengine-search-filters', // Base ID
			'WP Rengine Search Filters',
			['description' => 'The widget for the search filters of the wp rengine'] // Args
		);
        $this->frontController = new FrontController();
	}

	public function widget($args, $instance) {
        if (is_page('wp-rengine-search-results')):
		$attributes = $this->frontController->getAllAttributes();
		$brands     = $this->frontController->getAllBrands();
		?>
            <form class="wp-rengine-search-filter" method="get">
            <?php if ($brands): ?>
                <h4><?php _e( 'brands', 'wordpress-rengine' ) ?></h4>
                <?php foreach ($brands->data as $brand): ?>
                <input name="brand[<?php echo $brand->id ?>]" id="brand[<?php echo $brand->id ?>]" type="checkbox" <?php echo (isset($_GET['brand'][$brand->id])) ? 'checked' : '' ; ?>>
                <label for="brand[<?php echo $brand->id ?>]"><?php echo $brand->name; ?></label><br>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($attributes): ?>
                <h4><?php _e( 'attributes', 'wordpress-rengine' ) ?></h4>
                <?php foreach ($attributes->data as $attribute): ?>
                <input name="attribute[<?php echo $attribute->id ?>]" id="attribute[<?php echo $attribute->id ?>]" type="checkbox" <?php echo (isset($_GET['attribute'][$attribute->id])) ? 'checked' : '' ; ?>>
                <label for="attribute[<?php echo $attribute->id ?>]"><?php echo $attribute->value .' ', $attribute->name; ?></label> <br>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php foreach ($_GET as $key => $value): ?>
            	<?php if (!is_array($value)): ?>
            		<input type="hidden" name="<?php echo $key ?>" value="<?php echo $value ?>" />
            	<?php endif; ?>
            <?php endforeach ?>
            </form>
            <?php
        endif;
}

	public function form($instance) {
		// outputs the options form on admin
	}

	public function update($new_instance, $old_instance) {
		// processes widget options to be saved
	}

}