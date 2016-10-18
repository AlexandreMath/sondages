<?php
require_once "models/MessageModel.inc.php";
require_once "actions/Action.inc.php";

class UpdateUserFormAction extends Action {

	/**
	 * Dirige l'utilisateur vers le formulaire de modification de mot de passe.
	 *
	 * @see Action::run()
	 */
	public function run() {
            
            // Si la variable de session "login" est à null, on demande à l'utilisateur de s'identifier
		if ($this->getSessionLogin()=== null) {
			$this->setMessageView("Vous devez être authentifié.");
			return;
		}
            
            // Définition du modèle
		$this->setModel(new MessageModel());
            
            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
		$this->getModel()->setLogin($this->getSessionLogin());
            
            // Définition de la vue
		$this->setView(getViewByName("UpdateUserForm"));
	}

}
?>
