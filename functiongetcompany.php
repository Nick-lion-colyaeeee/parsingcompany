<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/atofighi/phpquery/phpQuery/phpQuery.php';
use GuzzleHttp\Client as Client;
header('Content-Type: text/html; charset=utf-8');
$companyarray = array();
function rutranslit($title)
{
    $chars = [
        //rus
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'Ё' => 'YO',
        'Ж' => 'ZH',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'Y',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Х' => 'KH',
        'Ц' => 'C',
        'Ч' => 'CH',
        'Ш' => 'SH',
        'Щ' => 'SHH',
        'Ъ' => '',
        'Ы' => 'Y',
        'Ь' => '',
        'Э' => 'YE',
        'Ю' => 'YU',
        'Я' => 'YA',
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'yo',
        'ж' => 'zh',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'kh',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'shh',
        'ъ' => '',
        'ы' => 'y',
        'ь' => '',
        'э' => 'ye',
        'ю' => 'yu',
        'я' => 'ya',
        //spec
        '—' => '-',
        '«' => '',
        '»' => '',
        '…' => '',
        '№' => 'N',
        '—' => '-',
        '«' => '',
        '»' => '',
        '…' => '',
        '!' => '',
        '@' => '',
        '#' => '',
        '$' => '',
        '%' => '',
        '^' => '',
        '&' => '',
        '"' => '',
        //ukr
        'Ї' => 'Yi',
        'ї' => 'i',
        'Ґ' => 'G',
        'ґ' => 'g',
        'Є' => 'Ye',
        'є' => 'ie',
        'І' => 'I',
        'і' => 'i',
        //kazakh
        'Ә' => 'A',
        'Ғ' => 'G',
        'Қ' => 'K',
        'Ң' => 'N',
        'Ө' => 'O',
        'Ұ' => 'U',
        'Ү' => 'U',
        'H' => 'H',
        'ә' => 'a',
        'ғ' => 'g',
        'қ' => 'k',
        'ң' => 'n',
        'ө' => 'o',
        'ұ' => 'u',
        'h' => 'h'
    ];
    $title = preg_replace('/\s/', '-', $title);
    $title = mb_strtolower($title);
    return strtr($title, $chars);
}/*funtion literals*/


function getcompany($htmlcompany, $catecony_name_directory)
{
    $http_client = new Client();
    $response = $http_client->request('GET', $htmlcompany);
    $name = $response->getBody(true);
    $d = phpQuery::newDocumentHTML($name);
    $td9 = $d->find("div[class=inf-list-01] dt");
    $companyarray['title'] = '-';
    $companyarray['url'] = $htmlcompany;
    $companyarray['site'] = '-';
    $companyarray['whileworking'] = '-';
    $companyarray['phone'] = '-';

    $companyarray['url_images'] = '-';
    $companyarray['company_address_lng'] = '-';
    $companyarray['phone'] = '-';
    $companyarray['addresses'] = '-';
    $companyarray['company_address_lng'] = '-';
    $companyarray['company_address_lat'] = '-';
    $companyarray['company_deskription'] = '-';


    $d->find("div[class=reviews-01]")->remove();

    $companyarray['company_deskription'] = $d->find("div[class=box]")->text();
//
    if (!empty($title_name)) {
        $companyarray['title'] = $title_name; /*name is company*/
    } else {
        $companyarray['title'] = $d->find("ul[class=breadcrumbs] li:last-child")->html();;
    }

//
//
    foreach ($td9 as $key_td9) {

//        echo pq($key_td9)->find("div[class=numb-01]")->text();
        $pq9 = pq($key_td9)->find("span")->text();
        switch ($pq9) {
            case "Телефоны:":
                $d->find("div[class=numb-01] span")->remove();
                $companyarray['phone'] = trim(pq($key_td9)->next()->text());
                break;
            case "Адрес:":
                $companyarray['addresses'] = trim(pq($key_td9)->next()->text());
                $ling = pq($key_td9)->next()->find('a')->attr('data-lng');

                if (!empty($ling)) {
                    $companyarray['company_address_lng'] = pq($key_td9)->next()->find('a')->attr('data-lng');
                    $companyarray['company_address_lat'] = pq($key_td9)->next()->find('a')->attr('data-lat');
                } else {
                    echo 1222;
                    $location_sript = $d->find("script")->text();
//                   die;
                    $start_replase = strripos($location_sript, "lat:");
                    $end_replase = strripos($location_sript, "},
                                    zoom");
                    $difference = $end_replase - $start_replase;// riznuchi miz strocamy
                    $srting_cordination = substr($location_sript, $start_replase, $difference);
                    $mas_cordination = explode(',', $srting_cordination);
                    $companyarray['company_address_lng'] = preg_replace('/[^0-9\.]/', '', $mas_cordination[0]);
                    $companyarray['company_address_lat'] = preg_replace('/[^0-9\.]/', '', $mas_cordination[1]);
//                    var_dump( $companyarray['company_address_lng']);
                    if (empty($companyarray['company_address_lng'])) {
                        $companyarray['company_address_lng'] = '-';
                        $companyarray['company_address_lat'] = '-';
                    }

                }

                break;
            case "Сайт:":
                $companyarray['site'] = pq($key_td9)->next()->text();;
                break;
            case "Почта:":

                $companyarray['mail'] = pq($key_td9)->next()->text();
                break;
            case "График работы":
                $companyarray['whileworking'] = pq($key_td9)->next()->text();
                break;

        }
        $i = 1;


    }
    $images = "images";

    @ mkdir($images . '/' . $catecony_name_directory, 0777);
    $dir = __DIR__ . "\\images\\" . $catecony_name_directory;/*all dir name local diretory*/;
    $dir_locatio = "\\images\\" . $catecony_name_directory;/*src image in bd location*/
    $src_image_pay = $d->find("div[class=logo-store] img")->attr('src');

    $src_images_div = $d->find("div[class=sale-holder] div[class=img] img")->attr('src');

    if (!empty($src_image_pay)) {

        copy($src_image_pay, $dir . DIRECTORY_SEPARATOR . rutranslit($companyarray['title']). ".png");
        $url_images = $dir_locatio . DIRECTORY_SEPARATOR .rutranslit($companyarray['title']). ".png";
        $companyarray['url_images'] = $url_images;
        echo "12345";

    } elseif (!empty($src_images_div)) {

        copy($src_images_div, $dir . DIRECTORY_SEPARATOR . rutranslit($companyarray['title']). ".png");
        $url_images = $dir_locatio . DIRECTORY_SEPARATOR . rutranslit($companyarray['title']). ".png";
        $companyarray['url_images'] = $url_images;
        echo "5678";


    } else {
        $url_liters = $d->find("div[class=img] a span")->eq(0)->text();

//        $url2 = "http://parsingcompany/dummyimage-master/400x40/2222/fff.png&text=" . $url_liters;
        $url2 = "https://dummyimage.com/200/2222/fff.png&text=" . $url_liters;
        copy($url2, $dir . DIRECTORY_SEPARATOR . rutranslit($url_liters) . ".png");
        $url_images = $dir_locatio . DIRECTORY_SEPARATOR . rutranslit($url_liters) . ".png";
        $companyarray['url_images'] = $url_images;

        echo "8910";
    }

// echo $d->find("div[class=sale-holder] div[class=img]")->html();
//    $catecony_name_directory="svadebnie-agenstva";


    $pq8 = $d->find("p")->eq(0)->text();

    return $companyarray;
}



var_dump($companyarray);
