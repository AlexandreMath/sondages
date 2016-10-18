<?php

require_once "models/SurveysModel.inc.php";
require_once "actions/Action.inc.php";

class GetMySurveysAction extends Action {

	/**
	 * Construit la liste des sondages de l'utilisateur dans un modèle
	 * de type "SurveysModel" et le dirige vers la vue "SurveysView" 
	 * permettant d'afficher les sondages.
	 *
	 * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
	 *
	 * @see Action::run()
	 */
	public function run() {

            // Si la variable de session "login" est à null, on demande à l'utilisateur de s'identifier            
                if ($this->getSessionLogin()===null) {
                    $this->setMessageView("Vous devez être authentifié.");
                    return;
                }
                
            // On tente de récupérer les sondages de l'utilisateur dans la DB  
                $reponseDB = $this -> database -> loadSurveysByOwner( $this->getSessionLogin() );
            
            
            // Si aucune erreur n'est survenue lors de la récupération des sondages dans la DB, on affiche ceux-ci
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
	}

}

?>
