<?php

require_once("models/SurveysModel.inc.php");
require_once("actions/Action.inc.php");

class VoteAction extends Action {

	/**
	 * Récupère l'identifiant de la réponse choisie par l'utilisateur dans la variable
	 * $_POST["responseId"] et met à jour le nombre de voix obtenues par cette réponse.
	 * Pour ce faire, la méthode 'vote' de la classe 'Database' est utilisée.
	 * 
	 * Si une erreur se produit, un message est affiché à l'utilisateur lui indiquant
	 * que son vote n'a pas pu être pris en compte.
	 * 
	 * Sinon, un message de confirmation lui est affiché.
	 *
	 * @see Action::run()
	 */	
	public function run() {
            
            // On initialise la variable "responseId" à chaine vide.
		$responseId = "";
                
            // Si une réponse a été choisie par l'utilisateur, on affecte sa valeur (donc son id) à la variable "responseId"
		if (isset($_POST["responseId"])) $responseId = (int)trim($_POST["responseId"]);
                
            // Si l'utilisateur a choisit une réponse, on tente d'ajouter le vote dans la DB
                if ( $responseId != '' ) {
                    $reponseDB = $this->database->vote((int)$responseId);
                } else {
                    $this->setMessageView("Aucune réponse n'a été cochée.");
                    return; 
                }
                
            // Si le vote n'a pu être ajouté, on affiche un message d'erreur
		if ($reponseDB === false) {
                    $this->setMessageView("Impossible d'enregistrer votre vote.");
                    return;
		}
            
            // Si le vote à correctement été ajouté, on affiche un message de réussite
		$this->setMessageView("Votre vote a été enregistré.");
                
	}

}

?>