<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Abraham\TwitterOAuth\TwitterOAuth;

class DefaultController extends Controller
{

    public $fib = array(0, 1);

    function __construct()
    {
        $this->setFib();
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/{handle1}/{handle2}/{method}")
     */
    public function netpositiveAction($handle1='', $handle2='', $method = 'fib')
    {

        $twitterList = array();

        $jokes = array();
        array_push($jokes, array('icndb', '', $this->randomJoke()));

        $ConsumerKey = 'qENAF2DglBNZD42RVrR1Pb3uR';
        $ConsumerSecret = '8jWZlWeM9wHHDeTHEk3bh0aiJ98FwPbkBa9py4d9xLWX7mxF00';
        $AccessToken = '958016141124829184-LpfkABjXbhyqNy1t0x0xOSjaPVs1x4f';
        $AccessTokenSecret = 'C72h1KnhuxiXdjLhV2eDN3LYgUFfHbRAFJYGWoamMmSZY';

        $connection = new TwitterOAuth($ConsumerKey, $ConsumerSecret, $AccessToken, $AccessTokenSecret);
        $content = $connection->get("account/verify_credentials");
        $twitter1 = $connection->get("search/tweets", ["q" => $handle1, "count" => "20"]);
        $twitter2 = $connection->get("search/tweets", ["q" => $handle2, "count" => "20"]);

        foreach ($twitter1->statuses as $item) {
            $time = $item->created_at;
            $time = new \DateTime($time);
            array_push($twitterList, ["date" => $time->format('Y.m.d H:i'), "text" => $item->text, "site" => "twitter/".$handle1]);
        }

        foreach ($twitter2->statuses as $item) {
            $time = $item->created_at;
            $time = new \DateTime($time);
            array_push($twitterList, ["date" => $time->format('Y.m.d H:i'), "text" => $item->text, "site" => "twitter/".$handle2]);
        }

        asort($twitterList);

//        echo '<pre>';
//        var_dump($this->sortByMethod($twitterList, $method));
//        die;

        return $this->render('default/netpositive.html.twig', [
            'list' => $this->sortByMethod($twitterList, $method),
        ]);

    }

    private function randomJoke()
    {
        $jokes_url = file_get_contents('http://api.icndb.com/jokes/random');
        $jokes = json_decode($jokes_url);
        return $jokes->value->joke;
    }

    private function sortByMethod($list, $method)
    {
        $ret = array();

        if ($method == 'fib') {
            foreach ($list as $k => $v) {
                array_push($ret, $v);
                if (in_array($k,$this->fib)) {
                    array_push($ret, ["date" => "", "text" => $this->randomJoke(), "site" => "icndb"]);
                }
            }
        }
        if ($method == 'mod') {
            foreach ($list as $k => $v) {
                array_push($ret, $v);
                if (is_int($k / 2)) {
                    array_push($ret, ["date" => "", "text" => $this->randomJoke(), "site" => "icndb"]);
                }
            }
        }
        return $ret;
    }

    private function setFib()
    {
        for ($i = 2; $i <= 10; ++$i) {
            $this->fib[$i] = $this->fib[$i - 1] + $this->fib[$i - 2];
        }
    }

}
