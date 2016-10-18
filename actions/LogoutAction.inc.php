<?php 

require_once("models/Model.inc.php");
require_once("actions/Action.inc.php");

class LogoutAction extends Action {

	/**
	 * Déconnecte l'utilisateur courant. Pour cela, la valeur 'null'
	 * est affectée à la variable de session 'login' à l'aide d'une méthode
	 * de la classe Action.
	 *
	 * @see Action::run()
	 */	
	public function run() {
            
            // Affectation de la valeur "null" à la variable de session "login"
		$this->setSessionLogin(null);
            
            // Définition du modèle
		$this->setModel(new Model());
            
            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
		$this->getModel()->setLogin($this->getSessionLogin());
            
            // Définition de la vue
		$this->setView(getViewByName("Default"));
	}

}


