<?php
namespace WPRengine;

class CustomTwigExtension extends \Twig_Extension {

	public function getFunctions() {
		return [
			'header'  => new \Twig_SimpleFunction('header', [$this, 'header']),
			'footer'  => new \Twig_SimpleFunction('footer', [$this, 'footer']),
			'sidebar' => new \Twig_SimpleFunction('sidebar', [$this, 'sidebar']),
		];
	}

	public function header($string = null) {
		return get_header($string);
	}

	public function footer($string = null) {
		return get_footer($string);
	}

	public function sidebar($string = null) {
		return dynamic_sidebar($string);
	}

	public function getName() {
		return 'template';
	}

}