<?php

require_once("models/SurveysModel.inc.php");
require_once("actions/Action.inc.php");

class SearchAction extends Action {

	/**
	 * Construit la liste des sondages dont la question contient le mot clé
	 * contenu dans la variable $_POST["keyword"]. Cette liste est stockée dans un modèle
	 * de type "SurveysModel". L'utilisateur est ensuite dirigé vers la vue "ServeysView"
	 * permettant d'afficher les sondages.
	 *
	 * Si la variable $_POST["keyword"] est "vide", le message "Vous devez entrer un mot clé
	 * avant de lancer la recherche." est affiché à l'utilisateur.
	 *
	 * @see Action::run()
	 */
	public function run() {
            
            // Récupération du mot-clef entré dans le formulaire de recherche 
                $keyword = $_POST['keyword'];
            
            // On vérifie si le mot clef a été entré correctement
                if ( isset( $keyword ) && trim($keyword) != '' )  {

                    // On tente de récupérer les sondages contenant le mot-clef dans la DB 
                        $reponseDB = $this -> database -> loadSurveysByKeyword( $keyword );
                    
                    // Si aucun erreur n'est survenue lors de la récupération des sondages dans la DB, on affiche ceux-ci
                        if ( $reponseDB !== false ){
                            
                            // Définition du modèle
                                $this -> setModel( new SurveysModel() );
                    
                            // Affectation du tableau de sondages récupéré à la propriété "surveys" du modèle
                                $this -> getModel() -> setSurveys ( $reponseDB );

                            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
                                $this->getModel()->setLogin($this->getSessionLogin());
                            
                            // Définition de la vue
                                $this -> setView( getViewByName('Surveys') );
                                
                        } else {
                            
                            // On affiche un message d'erreur à l'utilisateur 
                                $this->setMessageView("Une erreur s'est produite."); 	
                        }

                } else {
                    
                    // On affiche un message d'erreur à l'utilisateur 
                        $this->setMessageView("Vous devez entrer un mot clé avant de lancer la recherche.");        
                }
            
	}

}

?>
