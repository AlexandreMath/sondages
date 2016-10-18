<?php
require_once("models/Model.inc.php");
require_once("actions/Action.inc.php");

class DefaultAction extends Action {

	/**
	 * Traite l'action par défaut. 
	 * Elle dirige l'utilisateur vers une page avec un contenu vide.
	 *
	 * @see Action::run()
	 */
	public function run() {
            
            // Définition du modèle
		$this->setModel(new Model());
            
            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
		$this->getModel()->setLogin($this->getSessionLogin());
                
            // Définition de la vue
		$this->setView(getViewByName("Default"));
	}

}
?>
