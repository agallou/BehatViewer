<?php
namespace BehatViewer\BehatViewerBundle\Extension\Twig;

use Symfony\Component\DependencyInjection\ContainerAwareInterface,
    Symfony\Component\DependencyInjection\ContainerInterface;;

/**
 *
 */
class BehatViewerTwigExtension extends \Twig_Extension implements ContainerAwareInterface
{
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

  /**
     * @return string
     */
    public function getName()
    {
        return 'behat_viewer_ext';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'slugify' => new \Twig_Filter_Method($this, 'slugify'),
            'clean' => new \Twig_Filter_Method($this, 'clean'),
            'basename' => new \Twig_Filter_Function('basename'),
            'ceil' => new \Twig_Filter_Function('ceil'),
            'dump' => new \Twig_Filter_Function('var_dump'),
            'round' => new \Twig_Filter_Method($this, 'round'),
            'iconv' => new \Twig_Filter_Method($this, 'iconv'),
            'count' => new \Twig_Filter_Function('count'),
            'nl2br' => new \Twig_Filter_Function('nl2br'),
            'gravatar' => new \Twig_Filter_Method($this, 'getGravatarImage'),
            'stepify' => new \Twig_Filter_Method($this, 'stepify'),
        );
    }

    public function getGravatarImage($email, $size = 32, $rating = 'G')
    {
        return  $grav_url = "http://www.gravatar.com/avatar/" . md5(strtolower(trim( $email))) . "&s=" . $size . '&r=' . $rating;
    }

    public function getGlobals()
    {
        return array(
          'domain' => $this->container->get('request')->getHost()
        );
    }

    /**
     * @param float $value
     * @param int   $dec
     *
     * @return float
     */
    public function round($value, $dec = 1)
    {
        return round((float) $value, $dec);
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function slugify($text)
    {
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = $this->iconv($text);
        $text = strtolower($text);
        $text = preg_replace('~[^-\w]+~', '', $text);

        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function clean($text)
    {
        $text = str_replace(array('?P'), '', $text);
        $text = preg_replace('/\\\\\w/', '', $text);
        $text = str_replace('|', ' ', $text);
        $text = preg_replace('/[^\s\w]+/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        $text = $this->iconv($text);
        $text = strtolower($text);
        $text = preg_replace('/[^-\w\s]+/', '', $text);

        return $text;
    }

    public function stepify($text)
    {
        $text = trim($text);

        $text = preg_replace('/\(\?P<(\w*)>(\([^\)]*\)|[^\)]*)*\)/', '<strong>$1</strong>', $text);
        $text = preg_replace('/(Given|Then|And|When|But)\s*.{1}\^/is', '<strong>$1</strong> ', $text);
        $text = preg_replace('/\$.{1}$/is', '', $text);
        $text = preg_replace('/"(\w*)\W*(\w*)"/is', '"$1$2"', $text);
        $text = preg_replace('/(\w{1})\?/', '($1)', $text);
        $text = preg_replace_callback(
            '/\(\?i\)(\w*)\(\?\-i\)/',
            function($matches) {
                return strtoupper($matches[1]);
            },
            $text
        );
        $text = preg_replace_callback(
            '/\(\?:([^\)]*)\)/',
            function($matches) {
                $matches = explode('|', $matches[1]);
                $matches = array_filter($matches);

                return current($matches);
            },
            $text
        );
        $text = str_replace('*")', '', $text);

        return $text;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function iconv($text)
    {
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        return $text;
    }
}
