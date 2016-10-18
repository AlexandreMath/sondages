<?php

require_once("models/MessageModel.inc.php");
require_once("actions/Action.inc.php");

class AddSurveyFormAction extends Action {

	/**
	 * Traite les données envoyées par le formulaire d'ajout de sondage.
	 *
	 * Si l'utilisateur n'est pas connecté, un message lui demandant de se connecter est affiché.
	 *
	 * Sinon, la fonction ajoute le sondage à la base de données. Elle transforme
	 * les réponses et la question à l'aide de la fonction PHP 'htmlentities' pour éviter
	 * que du code exécutable ne soit inséré dans la base de données et affiché par la suite.
	 *
	 * Un des messages suivants doivent être affichés à l'utilisateur :
	 * - "La question est obligatoire.";
	 * - "Il faut saisir au moins 2 réponses.";
	 * - "Merci, nous avons ajouté votre sondage.".
	 *
	 * Le visiteur est finalement envoyé vers le formulaire d'ajout de sondage pour lui
	 * permettre d'ajouter un nouveau sondage s'il le désire.
	 *
	 * @see Action::run()
	 */
	public function run() {
           
            // Si la variable de session "login" est à null, on demande à l'utilisateur de s'identifier		
		if ($this->getSessionLogin()===null) {
			$this->setMessageView("Vous devez être authentifié.");
			return;
		}
            
            // Définition du modèle
		$this->setModel(new MessageModel());
            
            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
		$this->getModel()->setLogin($this->getSessionLogin());
                
            // Définition de la vue
		$this->setView(getViewByName("AddSurveyForm"));
	}

}

?>
