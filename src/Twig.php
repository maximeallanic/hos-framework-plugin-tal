<?php
/**
 * Created by PhpStorm.
 * User: mallanic
 * Date: 01/04/16
 * Time: 16:48
 */

namespace Hos\Plugin\Twig;


use Assetic\AssetWriter;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Factory\LazyAssetManager;
use Hos\Chronometer;
use Hos\Plugin\Twig\Extension\Stylesheet;
use Hos\Plugin\Twig\Extension\Javascript;
use Hos\Option;
use Twig_Environment;
use Twig_Lexer;
use Twig_Loader_Filesystem;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\FilterManager;
use Assetic\Factory\AssetFactory;
use Hos\Plugin\Twig\Extension\AsseticExtension;
use Twig_Extension_Optimizer;

class Twig
{
	/**
	 * [$twig description]
	 * @var Twig_Environment
	 */
    private $twig;

		/**
		 * [$factory description]
		 * @var AssetFactory
		 */
    private $factory;
    private $twigLoader;

    function __construct()
    {

        $am = new AssetManager();
        $am->set('vendor', new AssetCollection(Option::get("vendor.javascripts.import", [])));

        $fm = new FilterManager();

				$cssFilter = new Stylesheet();

        $jsFilter = new Javascript();

				$fm->set('stylesheets', $cssFilter);
				$fm->set('javascripts', $jsFilter);

        $this->factory = new AssetFactory(Option::ASSET_DIR);
        $this->factory->setAssetManager($am);
        $this->factory->setFilterManager($fm);
        $this->factory->setDebug(Option::isDev());

				/** Load Import Path **/
        $this->twigLoader = new Twig_Loader_Filesystem(Option::get("dir.base.asset", [Option::ASSET_DIR]));

        /** Set For Environment */
        $this->twig = new Twig_Environment($this->twigLoader, Option::get('twig.environment', [], [
					"cache" => Option::TEMPORARY_DIR,
					'debug' => Option::isDev(),
					'optimizations' => -1
				]));

        /** Customize Twig */


        //$this->twig->addGlobal('api', new Api());
        $this->twig->addGlobal('app', new Option());

        $this->twig->addExtension(new AsseticExtension($this->factory));

				$this->twig->addExtension(new Twig_Extension_Optimizer());

        $lexer = new Twig_Lexer($this->twig, Option::get('twig.lexer', []));
        $this->twig->setLexer($lexer);
    }

    function render($file, $array = [], $path = Option::ASSET_DIR) {
			Chronometer::start("twig");
        $cache = Option::TEMPORARY_ASSET_DIR.md5($file);

				if (Option::isDev() || !file_exists($cache)) {
					$am = new LazyAssetManager($this->factory);
					$am->setLoader('twig', new TwigFormulaLoader($this->twig));
					$resource = new TwigResource($this->twigLoader, $file);
					$am->addResource($resource, 'twig');
					$writer = new AssetWriter(Option::TEMPORARY_ASSET_DIR);
					$writer->writeManagerAssets($am);
					file_put_contents($cache, '');
				}

				$this->twigLoader->prependPath($path);
				Chronometer::end("twig");
				$render = $this->twig->render($file, $array);
				Chronometer::end("twig");
				return $render;
    }
}
