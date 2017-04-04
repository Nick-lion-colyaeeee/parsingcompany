<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/atofighi/phpquery/phpQuery/phpQuery.php';
header('Content-Type: text/html; charset=utf-8');

require_once 'getcategory.php';
require_once 'functiongetcompany.php';
use GuzzleHttp\Client as Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Stream;


$jar = new \GuzzleHttp\Cookie\CookieJar;

$cookieFile = $_SERVER['DOCUMENT_ROOT'] . '/cookies/materikkion.txt';

$http_client = new Client(['base_uri' => 'http://dnepr.info']);

$Allcompany=array();
$category_companys=get_companry();

$a=0;
$array_category_company_url = array();
for ($b=0;$b<1;$b++) {

    $start = microtime(true);
    $key = 5;

   $ostacha = $category_companys[$b]['sub'] % 20;

     $chile_procesing_count = ($category_companys[$b]['sub'] - $ostacha) / 20;

    $response = $http_client->request('GET', $category_companys[$b]['ling']);


    $n = $response->getBody(true);


    $d = phpQuery::newDocumentHTML($n);


    $td = $d->find("div[class=popular-store] div[class=store]");

    foreach ($td as $key) {
        $ArrayUrl[]=pq($key)->find("a[class=a-title]")->attr('href');
    }

    $td1 = $d->find("div[class=popular-store ajax-box] div[class=store]");


    foreach ($td1 as $key) {
        $ArrayUrl[]=pq($key)->find("a[class=a-title]")->attr('href');
    }

    for ($i = 0; $i < $chile_procesing_count; $i++) {
        $response = $http_client->request('POST', 'http://dnepr.info/wp-content/themes/dnepr/ajax/ajax_products_page.php', ['form_params' => ['offset' => '20', 'date' => '', 'term_id' => $category_companys[$b]['text_english'], 'menu_id' => '88419',]]);

        $n = $response->getBody(true);
        $d = phpQuery::newDocumentHTML($n);
        $td3 = $d->find("div[class=store]");
        foreach ($td3 as $key) {
            $ArrayUrl[]=pq($key)->find("a[class=a-title]")->attr('href');
        }

    }
    $resultatAllcompany=array_unique($ArrayUrl);
    foreach ($resultatAllcompany as $keysAllcompany)
    {
        $Allcompany[$category_companys[$b]['text_english']][]=getcompany($keysAllcompany,$category_companys[$b]['text_english']);

    }

}

