<?php
namespace Hos\Plugin\Twig;

use Hos\Dispatcher;
use Hos\Error;
use Hos\Option;
use League\Flysystem\Util\MimeType;

/**
  * @author Maxime Allanic maxime@allanic.me
  * @license GPL
  * @internal Created 2016-07-15 12:48:28
  */
class Events{
	static private $twig = null;

	static function generate($arguments) {
		if (!isset($arguments['file']) && isset($arguments['originalFile'])) {
			$info = pathinfo($arguments['originalFile']);
			$arguments['file'] = $info["dirname"]."/".$info["filename"].".twig";
			Dispatcher::dispatch('header.add', array(
				"Content-Type" => MimeType::detectByFilename($arguments['originalFile']),
				"Pragma" => "public",
				"Cache-Control" => "public",
				"Expires" => date('r', time() + 604800),
				"Last-Modified" => date('r', filemtime(Option::ASSET_DIR.$arguments['file']))
			));
		}
		else if (!isset($arguments['file']))
			throw new Error("twig.no_file_input");
		return self::getTwig()->render(
			$arguments['file'],
			isset($arguments['global']) ? $arguments['global'] : [],
			$arguments['path']
		);
	}

	static private function getTwig() {
		if (!self::$twig)
			self::$twig = new Twig();
		return self::$twig;
	}
}
