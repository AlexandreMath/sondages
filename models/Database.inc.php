<?php
require_once "models/Survey.inc.php";
require_once "models/Response.inc.php";

class Database {

	private $connection;

	/**
	 * Ouvre la base de données. Si la base n'existe pas elle
	 * est créée à l'aide de la méthode createDataBase().
	 */
	public function __construct() {
                
                // Ouverture de la connection à la DB
                    $this->connection = new PDO("sqlite:database.sqlite");
		
                // En cas d'erreur de connection, on coupe tout et on affiche un message d'erreur
                    if (!$this->connection) die("impossible d'ouvrir la base de données");

                // Récupération du nom des tables contenues dans la DB
                    $resultat = $this->connection->query('SELECT name FROM sqlite_master WHERE type="table"');
                
                // Si la DB ne contient aucune table, on les crée au moyen de la méthode "createDatabase"
                    if (count($resultat->fetchAll()) == 0) {          
                        $this->createDataBase();           
                    }

	}


	/**
	 * Crée la base de données ouverte dans la variable $connection.
	 * Elle contient trois tables :
	 * - une table users(nickname char(20), password char(50));
	 * - une table surveys(id integer primary key autoincrement,
	 *						owner char(20), question char(255));
	 * - une table responses(id integer primary key autoincrement,
	 *		id_survey integer,
	 *		title char(255),
	 *		count integer);
	 */
	private function createDataBase() {
            
            // Création de la table "users"
                $requeteUsers = "CREATE TABLE users (nickname char(20), password char(50))"; 
                $this -> connection -> query($requeteUsers);
            
            // Création de la table "surveys"
                $requeteSurveys = "CREATE TABLE surveys ( id integer primary key autoincrement, owner char(20), question char(255))";
                $this -> connection -> query($requeteSurveys);
            
            // Création de la table "responses"
                $requeteResponses = "CREATE TABLE responses ( id integer primary key autoincrement, id_survey integer, title char(255), count integer)";
                $this -> connection -> query($requeteResponses);         
 	}

        
        
        
	/**
	 * Vérifie si un pseudonyme est valide, c'est-à-dire,
	 * s'il contient entre 3 et 10 caractères et uniquement des lettres.
	 *
	 * @param string $nickname Pseudonyme à vérifier.
	 * @return boolean True si le pseudonyme est valide, false sinon.
	 */
	private function checkNicknameValidity($nickname) {
                
            // On enlève les caractères invisibles de part et d'autre du "nickname" entré par l'internaute
                $nickname = trim($nickname);
            
            // On vérifie si le "nickname" possède bien entre 3 et 10 caractères
		if ( strlen( $nickname ) >= 3 && strlen( $nickname ) <= 10 ) {
                    
                    // On vérifie si le "nickname" possède bien uniquement des lettres
                        $regexNickname = '/^[a-zA-Z]*$/';
                    
                        if ( preg_match($regexNickname, $nickname) ) {
                            return true;                   
                        }
                    
                }
                
		return false;
                
	}

	/**
	 * Vérifie si un mot de passe est valide, c'est-à-dire,
	 * s'il contient entre 3 et 10 caractères.
	 *
	 * @param string $password Mot de passe à vérifier.
	 * @return boolean True si le mot de passe est valide, false sinon.
	 */
	private function checkPasswordValidity($password) {
            
            // On enlève les caractères invisibles de part et d'autre du "password" entré par l'internaute
                $password = trim($password);

            // On vérifie si le "password" possède bien entre 3 et 10 caractères
                if ( strlen( $password ) >= 3 && strlen( $password ) <= 10 ) {       
                        return true;                   
                }
                
		return false;      
	}

	/**
	 * Vérifie la disponibilité d'un pseudonyme.
	 *
	 * @param string $nickname Pseudonyme à vérifier.
	 * @return boolean True si le pseudonyme est disponible, false sinon.
	 */
	private function checkNicknameAvailability($nickname) {
            
            // Recherche dans la DB d'une ligne possédant le même "nickname" que celui entré par l'internaute
                $requeteAvailabilityNickname = "SELECT * FROM users WHERE nickname=:pseudo";
                $resultat = $this -> connection -> prepare($requeteAvailabilityNickname);
                $resultat -> execute( array( $nickname ));
            
            // Si aucune ligne n'a été trouvée, on renvoie TRUE, sinon FALSE               
                if ( count( $resultat->fetchAll() ) == 0 ) {
                    return true;
                } else {
                    return false;
                }

	}

	/**
	 * Vérifie qu'un couple (pseudonyme, mot de passe) est correct.
	 *
	 * @param string $nickname Pseudonyme.
	 * @param string $password Mot de passe.
	 * @return boolean True si le couple est correct, false sinon.
	 */
	public function checkPassword($nickname, $password) {
            
            // Recherche dans la DB d'une ligne possédant le nickname et le password donné en paramètre
                $requeteVerifIdentification = "SELECT * FROM users WHERE nickname=:pseudo AND password=:pass";
                $resultat = $this -> connection -> prepare( $requeteVerifIdentification );
                $resultat -> execute ( array( $nickname, md5($password) ) );


            // Si une ligne a été trouvée, on renvoie TRUE, sinon FALSE
                if ( count( $resultat -> fetchAll() ) != 0 ) {
                    return true;
                } else {
                    return false;
                }
            
	}
        

	/**
	 * Ajoute un nouveau compte utilisateur si le pseudonyme est valide et disponible et
	 * si le mot de passe est valide. La méthode peut retourner un des messages d'erreur qui suivent :
	 * - "Le pseudo doit contenir entre 3 et 10 lettres.";
	 * - "Le mot de passe doit contenir entre 3 et 10 caractères.";
	 * - "Le pseudo existe déjà.".
	 *
	 * @param string $nickname Pseudonyme.
	 * @param string $password Mot de passe.
	 * @return boolean|string True si le couple a été ajouté avec succès, un message d'erreur sinon.
	 */
	public function addUser($nickname, $password) {
            
            // On vérifie la validité du "nickname"
                if ( ! $this -> checkNicknameValidity($nickname) ) {           
                    return 'Le pseudo doit contenir entre 3 et 10 lettres.';
                }

            // On vérifie la validité du "password"
                if ( ! $this ->checkPasswordValidity($password) ) {           
                   return 'Le mot de passe doit contenir entre 3 et 10 caractères.';           
                }            
            
            // On vérifie la disponibilité du "nickname"
                if ( ! $this -> checkNicknameAvailability($nickname) ) {           
                    return 'Le pseudo existe déjà.';                        
                }            
            
            // Ajout de l'utilisateur dans la DB
                $requeteAjoutUsers = "INSERT INTO users (nickname, password) VALUES ( :pseudo, :pass ) ";
                $resultat = $this -> connection -> prepare($requeteAjoutUsers);
                $resultatAjout = $resultat -> execute( array( $nickname, md5($password) ));           
           
            
            // Si l'utilisateur a bien été ajouté dans la DB, renvoie TRUE sinon un message d'erreur
                if ( $resultatAjout === true ) {
                    return true;
                } else {
                    return 'Désolé, nous n\'avons pas pu vous inscrire.';  
                }
                
	}

	/**
	 * Change le mot de passe d'un utilisateur.
	 * La fonction vérifie si le mot de passe est valide. S'il ne l'est pas,
	 * la fonction retourne le texte 'Le mot de passe doit contenir entre 3 et 10 caractères.'.
	 * Sinon, le mot de passe est modifié en base de données et la fonction retourne true.
	 *
	 * @param string $nickname Pseudonyme de l'utilisateur.
	 * @param string $password Nouveau mot de passe.
	 * @return boolean|string True si le mot de passe a été modifié, un message d'erreur sinon.
	 */
	public function updateUser($nickname, $password) {
            
            // On vérifie la validité du "password"           
                if ( ! $this ->checkPasswordValidity($password) ) {           
                    return 'Le mot de passe doit contenir entre 3 et 10 caractères.';           
                }           
            
            // Modification du mot de passe dans la DB
                $requeteModifMDP = "UPDATE users SET password=:pass WHERE nickname=:pseudo ";
                $resultat = $this -> connection -> prepare( $requeteModifMDP );
                $resultatAjout = $resultat -> execute ( array( md5($password), $nickname ) );
                       
            
            // Si le mot de passe a bien été changé dans la DB, renvoie TRUE sinon un message d'erreur
                if ( $resultatAjout === true ) {
                    return true;
                } else {
                    return 'Désolé, nous n\'avons pas pu modifier votre mot de passe.';  
                }
	}

	/**
	 * Sauvegarde un sondage dans la base de donnée et met à jour les indentifiants
	 * du sondage et des réponses.
	 *
	 * @param Survey $survey Sondage à sauvegarder.
	 * @return boolean|string True si la sauvegarde a été réalisée avec succès, un message d'erreur sinon.
	 */
	public function saveSurvey(&$survey) {
            
            // Récupération des propriétés "owner" et "question" de l'objet "survey"
                $owner = $survey -> getOwner();
                $question = htmlentities ($survey -> getQuestion());
            
                
            // On regarde si le sondage ( owner - question ) existe déjà dans la DB
                $requeteDisponibiliteSurvey = "SELECT * FROM surveys WHERE owner=:owner AND question=:question";
                $resultat = $this -> connection -> prepare( $requeteDisponibiliteSurvey );
                $resultat -> execute ( array( $owner, $question ) );


            // Si aucune ligne n'a été trouvée, on tente d'ajouter le sondage, sinon on retourne un message d'erreur
                if ( count( $resultat -> fetchAll() ) == 0 ) {
                    
                    // On tente d'ajouter le sondage dans la DB
                        $requeteAjoutSondage = "INSERT INTO surveys (owner, question) VALUES (:pseudo, :question ) ";
                        $resultat = $this -> connection -> prepare($requeteAjoutSondage);
                        $resultatAjout = $resultat -> execute( array( $owner, $question ));	


                    // Si le sondage a bien été ajouté, on récupère son ID puis on ajoute ses réponses dans la DB
                        if ( $resultatAjout === true ) {

                            // Récupération de l'id du sondage dans la DB
                                $requeteRecupIdSondage = "SELECT id FROM surveys WHERE owner=:owner AND question=:question";
                                $resultat = $this -> connection -> prepare( $requeteRecupIdSondage );
                                $resultat -> execute ( array( $owner, $question ) );

                                $recupId = $resultat -> fetch();

                            // Affectation de l'id récupéré à la propriété "id" du sondage
                                $survey -> setId( $recupId['id'] );

                            // Ajout des réponses du sondage dans la DB
                                $tabReponses =  &$survey -> getResponses();

                                for ( $i = 0; $i < sizeOf( $tabReponses ); $i++ ){

                                    // Pour être sauvée dans la DB, chaque réponse du tableau "tabReponses" passe par la méthode "saveResponse"
                                        if ( ! $this -> saveResponse( $tabReponses[$i])) {
                                            return 'Désolé, nous n\'avons pu ajouter votre sondage.';;
                                        }
                                }

                            // Envoie de la valeur de retour TRUE si tout a bien été inséré dans la DB
                                return true;

                        } else {
                            return 'Désolé, nous n\'avons pu ajouter votre sondage.';
                        }   
                        
                } else {
                    return 'Désolé, vous avez déjà posté ce sondage.';
                }
            
            
	}

	/**
	 * Sauvegarde une réponse dans la base de donnée et met à jour son indentifiant.
	 *
	 * @param Response $response Réponse à sauvegarder.
	 * @return boolean True si la sauvegarde a été réalisée avec succès, false sinon.
	 */
	private function saveResponse(&$response) {
            
            // Récupération de la propriété "id" du sondage lié à la question
               $id_survey = $response -> getSurvey() -> getId();
            
            // Récupération des propriétés "title" et "count" de l'objet "response"
               $title = htmlentities ( $response -> getTitle());
               $count = $response -> getCount();
            
            
            // On tente d'ajouter la réponse dans la DB
               $requeteAjoutReponse = "INSERT INTO responses (id_survey, title, count) VALUES (:id, :title, :count ) ";
               $resultat = $this -> connection -> prepare($requeteAjoutReponse);
               $resultatAjout = $resultat -> execute( array( $id_survey, $title, $count )); 
            
            // Si la réponse a bien été ajouté dans la DB, retourne TRUE, sinon FALSE
                if ( $resultatAjout === true ) {
                   return true;
                } else {
                   return false;
                }
            
            
	}

	/**
	 * Charge l'ensemble des sondages créés par un utilisateur.
	 *
	 * @param string $owner Pseudonyme de l'utilisateur.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByOwner($owner) {
            
            // On tente de récupérer les sondages de l'utilisateur
                $requeteRecupSondagesUtilisateur = "SELECT * FROM surveys WHERE owner=:owner";
                $resultat = $this -> connection -> prepare( $requeteRecupSondagesUtilisateur );
                $resultatRecup = $resultat -> execute ( array( $owner) );            
               
                
            /* Si aucune erreur ne s'est produite lors de la récupération des sondages, 
            on continue, sinon retourne FALSE */
                if ( $resultatRecup === true ) {
                    
                    // Création d'un tableau de lignes contenant les sondages récupérés
                        $tableauSondages = $resultat->fetchAll(PDO::FETCH_ASSOC);
                            
                    /* On tente de récupérer un tableau d'objets de type "survey" à partir du tableau de lignes au moyen de la méthode "loadSurveys"
                    Si une erreur survient, on reçoit la valeur FALSE */
                        $reponseLoadSurveys = $this -> loadSurveys ( $tableauSondages );
                           
                        return $reponseLoadSurveys;
                            
                } else {
                    return false;
                }
                        
	}

	/**
	 * Charge l'ensemble des sondages dont la question contient un mot clé.
	 *
	 * @param string $keyword Mot clé à chercher.
	 * @return array(Survey)|boolean Sondages trouvés par la fonction ou false si une erreur s'est produite.
	 */
	public function loadSurveysByKeyword($keyword) {
            
            // On tente de récupérer les sondages dont la question contient le mot clef donné
                $requeteRecupSondageKeyword = "SELECT * FROM surveys WHERE question LIKE :keyword";
                $resultat = $this -> connection -> prepare( $requeteRecupSondageKeyword );
                $resultatRecup = $resultat -> execute ( array( '%'.$keyword.'%') );                      

            /* Si aucune erreur ne s'est produite lors de la récupération des sondages, 
            on continue, sinon retourne FALSE */
                if ( $resultatRecup === true ) {
                    
                    // Création d'un tableau de lignes contenant les sondages récupérés
                        $tableauSondages = $resultat->fetchAll(PDO::FETCH_ASSOC);
                            
                    /* On tente de récupérer un tableau d'objets de type "survey" à partir du tableau de lignes au moyen de la méthode "loadSurveys"
                    Si une erreur survient, on reçoit la valeur FALSE */
                        $reponseLoadSurveys = $this -> loadSurveys ( $tableauSondages );
                           
                        return $reponseLoadSurveys;
                
                } else {
                    return false;
                }        
    
	}


	/**
	 * Enregistre le vote d'un utilisateur pour la réponse d'indentifiant $id.
	 *
	 * @param int $id Identifiant de la réponse.
	 * @return boolean True si le vote a été enregistré, false sinon.
	 */
	public function vote($id) {
	
            // On tente de récupérer dans la DB le nombre de vote de la réponse dont l'id a été donné en paramètre
                $requeteRecupNbVote = "SELECT count FROM responses WHERE id=:id";
                $resultat = $this -> connection -> prepare( $requeteRecupNbVote );
                $resultatRecup = $resultat -> execute ( array( $id) );              
            
            
            /* Si aucune erreur ne s'est produite lors de la récupération du nombre de vote, 
            on continue, sinon retourne FALSE */
                if ( $resultatRecup === true ) {
               
                    // On récupère le nombre de vote dans une variable
                        $recupNbVote = $resultat -> fetch();
                    
                    // On tente d'ajouter dans la DB le nouveau nombre de vote
                        $requeteAjoutNbVote = "UPDATE responses SET count=:nbVote WHERE id=:id";
                        $resultat = $this -> connection -> prepare($requeteAjoutNbVote);
                        $resultatAjout = $resultat -> execute( array( ($recupNbVote['count'] + 1) , $id )); 
                   
                    // Si le vote a bien été ajouté dans la DB, retourne TRUE, sinon FALSE
                        if ( $resultatAjout === true ) {
                            return true;
                        } else {
                            return false;
                        }       
                        
               } else {
                   return false;
               }
            
	}

	/**
	 * Construit un tableau de sondages à partir d'un tableau de ligne de la table 'surveys'.
	 * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
	 *
	 * @param array $arraySurveys Tableau de lignes.
	 * @return array(Survey)|boolean Le tableau de sondages ou false si une erreur s'est produite.
	 */
	private function loadSurveys($arraySurveys) {
            
            // Déclaration de la variable tableau "tabSurveys" qui acceuillera les sondages
                $tabSurveys = array();
            
            
            // Pour chaque sondage récupéré, ...
                foreach ( $arraySurveys as $sondage ) {
                    
                    // On instancie un nouvel objet de type "survey"
                       $survey = new Survey( $sondage['owner'], $sondage['question'] );
                    
                    // On donne à la propriété "id" de l'objet "survey" l'id récupéré
                       $survey -> setId( $sondage['id'] );
                    
                    // On tente de récupérer dans la DB les réponses liées au sondage dont on a récupéré l'ID
                       $requeteRecupReponses = "SELECT * FROM responses WHERE id_survey=:id";
                       $resultat = $this -> connection -> prepare( $requeteRecupReponses );
                       $resultatRecup = $resultat -> execute ( array( $sondage['id'] ) );     

                        
                    /*  Si aucune erreur ne s'est produite lors de la récupération des réponses du sondage, 
                        on continue, sinon retourne FALSE */
                        if ($resultatRecup === true ) {
                            
                            // Création d'un tableau de lignes contenant les réponses récupérées
                                $tableauReponses = $resultat->fetchAll(PDO::FETCH_ASSOC);

                            // Récupération d'un tableau d'objets de type "response" partir du tableau de lignes au moyen de la méthode "loadResponses"
                                $tabResponses = $this -> loadResponses ( $survey, $tableauReponses );
                            
                            // On ajoute chaque réponse du tableau "tabResponses" au sondage
                                for ( $i = 0; $i < sizeOf($tabResponses); $i++ ) {
                                    $survey -> addResponse( $tabResponses[$i] );
                                }
                                
                            // On ajoute le sondage au tableau "tabSurveys"
                                array_push( $tabSurveys, $survey);

                        } else {
                            return false;
                        }  


                }
                
                // Lorsque toute les sondages ont été ajoutés au tableau "tabSurveys", on retourne le tableau de sondages
                    return $tabSurveys;
                
	}
        

	/**
	 * Construit un tableau de réponses à partir d'un tableau de ligne de la table 'responses'.
	 * Ce tableau a été obtenu à l'aide de la méthode fetchAll() de PDO.
	 *
         * @param Survey $survey Sondage.
	 * @param array $arrayReponses Tableau de lignes.
	 * @return array(Response) Le tableau de réponses.
	 */
	private function loadResponses(&$survey, $arrayResponses) {
            
            // Déclaration de la variable tableau "tabResponses" qui acceuillera les réponses
                $tabResponses = array();
            
            // Pour chaque réponse récupérée, ...
                foreach ($arrayResponses as $reponse ) {
                    
                    // On instancie un nouvel objet de type "response"
                       $response = new Response ( $survey, $reponse[ 'title' ], $reponse['count'] );
                    
                    // On donne à la propriété "id" de l'objet "response" l'id récupéré
                       $response -> setId( $reponse['id'] );
                       
                    // On ajoute le nouvel objet de type "response" au tableau "tabResponses"
                       array_push( $tabResponses, $response );
                } 
                
            
            // Lorsque toute les réponses ont été ajoutées au tableau "tabResponses", on retourne le tableau de réponses
                return $tabResponses;
                    
            }
        
       
}

?>
