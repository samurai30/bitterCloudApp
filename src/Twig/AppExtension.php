<?php
/**
 * Created by PhpStorm.
 * User: Samurai3095
 * Date: 2/12/2019
 * Time: 10:46 PM
 */

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtension extends  AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }
    public function priceFilter($number){
        return 'Rs:'.number_format($number, 2,'.',',');

    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return [
            'locale' => $this->locale
        ];
    }
}