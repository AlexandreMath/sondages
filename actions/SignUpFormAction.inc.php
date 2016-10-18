<?php

require_once("models/MessageModel.inc.php");
require_once("actions/Action.inc.php");

class SignUpFormAction extends Action {

	/**
	 * Dirige l'utilisateur vers le formulaire d'inscription.
	 *
	 * @see Action::run()
	 */	
	public function run() {
            
            // Définition du modèle
		$this->setModel(new MessageModel());
             
            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
		$this->getModel()->setLogin($this->getSessionLogin());
            
            // Définition de la vue
		$this->setView(getViewByName("SignUpForm"));
	}

}

?>
