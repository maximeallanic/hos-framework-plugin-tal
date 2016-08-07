<?php
namespace Hos\Plugin\Twig\Extension;


use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;
use Hos\Dispatcher;

/**
  * @author Maxime Allanic maxime@allanic.me
  * @license GPL
  * @internal Created 2016-07-30 17:48:54
  */
class Stylesheet implements FilterInterface{
	public function filterLoad(AssetInterface $asset) {

	}

	public function filterDump(AssetInterface $asset) {
		$asset->setContent(Dispatcher::dispatch("generate.css", [
				"content" => $asset->getContent(),
				"source" => $asset->getSourceRoot()."/".$asset->getSourcePath(),
				"global" => []
		], true));
		return $asset;
	}
}
