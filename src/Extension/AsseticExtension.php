<?php

namespace Hos\Plugin\Twig\Extension;

use Assetic\Factory\AssetFactory;
use Assetic\ValueSupplierInterface;
use Hos\Chronometer;

class AsseticExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    protected $factory;
    protected $functions;
    protected $valueSupplier;

    public function __construct(AssetFactory $factory, $functions = array(), ValueSupplierInterface $valueSupplier = null)
    {
        $this->factory = $factory;
        $this->functions = array();
        $this->valueSupplier = $valueSupplier;

        foreach ($functions as $function => $options) {
            if (is_integer($function) && is_string($options)) {
                $this->functions[$options] = array('filter' => $options);
            } else {
                $this->functions[$function] = $options + array('filter' => $function);
            }
        }
    }

    public function getTokenParsers()
    {
        return array(
            new AsseticTokenParser($this->factory, 'javascripts', 'js/*.js'),
            new AsseticTokenParser($this->factory, 'stylesheets', 'css/*.css'),
            new AsseticTokenParser($this->factory, 'image', 'images/*', true),
        );
    }

    public function getFunctions()
    {
        return [];
    }

    public function getGlobals()
    {
        return array(
            'assetic' => array(
                'debug' => $this->factory->isDebug(),
            ),
        );
    }

    public function getFilterInvoker($function)
    {

    }

    public function getName()
    {
        return 'assetic';
    }
}
