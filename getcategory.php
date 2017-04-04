<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/atofighi/phpquery/phpQuery/phpQuery.php';
header('Content-Type: text/html; charset=utf-8');
//require_once 'functiongetcompany.php';
//header('Content-Type: image/jpeg');

use GuzzleHttp\Client as Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Stream;

function get_companry()
{


    $http_client = new Client(['base_uri' => 'http://dnepr.info']);

    $response = $http_client->request('GET', 'http://dnepr.info/uslugi/');


    $n = $response->getBody(true);

    $d = phpQuery::newDocumentHTML($n);

    $category_companys = array();


    $td = $d->find("ul[class=child-org] li");

    $i = 0;


    foreach ($td as $key) {
        $category_companys[$i]['text'] = pq($key)->find("a")->text();

        $l = pq($key)->find("a")->attr('href');

        $category_companys[$i]['text_english'] = substr(strstr(str_replace('http://dnepr.info/uslugi/', ' ', $l), '/'), 1);/*pulled out of a links category eneglish name*/

        $category_companys[$i]['ling'] = pq($key)->find("a")->attr('href');

        $sub = pq($key)->find("a")->text();

        $category_companys[$i]['sub'] = (int)preg_replace("/[^0-9]/", '', $sub);

        $i++;
    }/*vzuv oll catecory company  masiv in url and number*/


    return $category_companys ;

}



/*vzuv oll catecory company  masiv in url and number*/