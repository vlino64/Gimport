<?php
	ini_set("session.cookie_lifetime","7200");
	ini_set("session.gc_maxlifetime","7200");
	session_start();
	require_once('./bbdd/connect.php');
	require_once('./func/constants.php');
	require_once('./func/generic.php');
        require_once ('./google/libraries/Google/autoload.php');

        function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
        }
        
	//Insert your cient ID and secret 
	//You can get it from : https://console.developers.google.com/
	$client_id = 'XXXXXXXXXXXXXXXXXXXX'; 
	$client_secret = 'XXXXXXXXXXXXXXXXXXXX';
	$redirect_uri = 'XXXXXXXXXXXXXXXXXXXX';
	$adrecaRetorn =  'XXXXXXXXXXXXXXXXXXXX';

	//incase of logout request, just unset the session var
	if (isset($_GET['logout'])) {
	  unset($_SESSION['access_token']);
	}

	/************************************************
	  Make an API request on behalf of a user. In
	  this case we need to have a valid OAuth 2.0
	  token for the user, so we need to send them
	  through a login flow. To do this we need some
	  information from our API console project.
	 ************************************************/
	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->addScope("email");
	$client->addScope("profile");

	/************************************************
	  When we create the service here, we pass the
	  client to it. The client then queries the service
	  for the required scopes, and uses that when
	  generating the authentication URL later.
	 ************************************************/
	$service = new Google_Service_Oauth2($client);

	/************************************************
	  If we have a code back from the OAuth 2.0 flow,
	  we need to exchange that with the authenticate()
	  function. We store the resultant access token
	  bundle in the session, and redirect to ourself.
	*/
	  
	if (isset($_GET['code'])) {
	  $client->authenticate($_GET['code']);
	  $_SESSION['access_token'] = $client->getAccessToken();
	  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
	  exit;
	}

	/************************************************
	  If we have an access token, we can make
	  requests, else we generate an authentication URL.
	 ************************************************/
	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	  $client->setAccessToken($_SESSION['access_token']);
	} else {
	  $authUrl = $client->createAuthUrl();
	}

		
		$user = $service->userinfo->get(); //get user info 

		//show user picture
		//echo $user->name.', Thanks for Registering! [<a href="./index.php?logout=1">Log Out</a>]';
		//echo "<br>".$user->email;

		if ($user->id != '') 
			{
			$sql = "SELECT idtipus_contacte FROM tipus_contacte WHERE Nom_info_contacte ='email';";
			$result = $db->query($sql);
			$fila = $result->fetch(); $idtipuscontacte = $fila['idtipus_contacte'];
			
			$sql  = "SELECT A.idprofessors AS idprof FROM professors A, contacte_professor B ";
			$sql .= "WHERE A.idprofessors=B.id_professor AND B.id_tipus_contacte = ".$idtipuscontacte." AND Valor = '".$user->email."' AND A.activat = 'S';";
			$result = $db->query($sql);
			$fila = $result->fetch(); $idprofessor = $fila['idprof'];
			$files = $result->rowCount();
						
			if (( $idprofessor != 0 ) AND ($files != 0))
				{
				$_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
				$_SESSION['curs_escolar_literal'] = getCursActual($db)->Nom;
				$_SESSION['professor']            = $idprofessor;
				$_SESSION['usuari']               = $idprofessor;  
				insertaLogProfessor($db,$_SESSION['professor'],TIPUS_ACCIO_LOGIN);
                                if(isMobile()){
                                        header('Location:mobi/home.php');
                                    }
                                    else {
                                        header('Location:home.php');
                                    }
                                
				}
			else 
				{
				$sql  = "SELECT A.idalumnes AS idalum FROM alumnes A, contacte_alumne B ";
				$sql .= "WHERE A.idalumnes=B.id_alumne AND B.id_tipus_contacte = ".$idtipuscontacte." AND Valor = '".$user->email."' AND A.activat = 'S';";
				$result = $db->query($sql);
				$fila = $result->fetch(); $idalumne = $fila['idalum'];
				$files = $result->rowCount();
				if (( $idalumne != 0 ) AND ($files != 0))
					{
					$_SESSION['curs_escolar']         = getCursActual($db)["idperiodes_escolars"];
					$_SESSION['curs_escolar_literal'] = getCursActual($db)->Nom;
					$_SESSION['alumne']            = $idalumne;
					$_SESSION['usuari']               = $idalumne;  
					insertaLogAlumne($db,$_SESSION['alumne'],TIPUS_ACCIO_LOGIN);
                                        if(isMobile()){
                                                header('Location:mobi/home.php');
                                            }
                                            else {
                                                header('Location:home.php');
                                            }
					}					
				else
					{
					header('Location: https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue='.$adrecaRetorn.'');
					} 	
				}
			}
	

	echo '</div>';


?>


