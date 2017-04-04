<?php
        require_once 'vendor/autoload.php';
        session_start();
  function getEvents(){
            $fb = new Facebook\Facebook([
                'app_id' => '1370666542978417',
                'app_secret' => '36ec06a68d69ed1518c4fbf784f4cfbf',
                'cookie' => true,
                'default_graph_version' => 'v2.8'
            ]);
            $events = array();
            $helper = $fb->getRedirectLoginHelper();
            $permissions = ['manage_pages', 'user_events', 'rsvp_event'];
            try {
                if (isset($_SESSION['facebook_access_token'])) {
                    $accessToken = $_SESSION['facebook_access_token'];
                } else {
                    $accessToken = $helper->getAccessToken();
                }
            } catch(Facebook\Exceptions\facebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
            if (isset($accessToken)) {
                if (isset($_SESSION['facebook_access_token'])) {
                    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
                }else if($helper->getError){echo 'ERROR';}
				else {
                    // getting short-lived access token
                    $_SESSION['facebook_access_token'] = (string) $accessToken;
                    // OAuth 2.0 client handler
                    $oAuth2Client = $fb->getOAuth2Client();
                    // Exchanges a short-lived access token for a long-lived one
                    $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
                    $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
                    // setting default access token to be used in script
                    $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
                }
                try {
                    $response = $fb->get('/me/events?fields=cover,description,end_time,name,place,start_time,id,rsvp_status');
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    echo 'Graph returned an error: ' . $e->getMessage();
                    session_destroy();
                    header("Location: ./");
                    exit;
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    echo 'Facebook SDK returned an error: ' . $e->getMessage();
                    exit;
                }
				$events = $response->getGraphEdge();//start
                $events = json_decode($events);
				
            } else {
				$loginUrl = $helper->getLoginUrl('http://misto.events/callMetod.php', $permissions); //eventsFacebook.php', $permissions);
                header('Location:'.$loginUrl);

            }
   return $events;
    }