<?php

require_once("models/MessageModel.inc.php");
require_once("actions/Action.inc.php");

class SignUpAction extends Action {

	/**
	 * Traite les données envoyées par le formulaire d'inscription
	 * ($_POST['signUpLogin'], $_POST['signUpPassword'], $_POST['signUpPassword2']).
	 *
	 * Le compte est crée à l'aide de la méthode 'addUser' de la classe Database.
	 *
	 * Si la fonction 'addUser' retourne une erreur ou si le mot de passe et sa confirmation
	 * sont différents, on envoie l'utilisateur vers la vue 'SignUpForm' avec une instance
	 * de la classe 'MessageModel' contenant le message retourné par 'addUser' ou la chaîne
	 * "Le mot de passe et sa confirmation sont différents.";
	 *
	 * Si l'inscription est validée, le visiteur est envoyé vers la vue 'MessageView' avec
	 * un message confirmant son inscription.
	 *
	 * @see Action::run()
	 */
	public function run() {
            
            // Récupération des données entrées dans le formulaire d'inscription
                $nickname = $_POST['signUpLogin'];
                $password1 = $_POST['signUpPassword'];
                $password2 = $_POST['signUpPassword2'];
            
            // On vérifie que les mots de passe entrés sont identiques
                if ( $password1 == $password2 ){ 
                    
                    // On tente d'ajouter dans la DB le nouvel utilisateur
                        $reponseDB = $this -> database -> addUser($nickname, $password1);
                    
                    // En fonction de l'ajout ou non de l'utilisateur, on définit un modèle et une vue affichant un message
                        if ( $reponseDB === true ) {

                            // Définition du modèle
                                $this->setModel(new MessageModel());

                            // Affectation d'un message de réussite à la propriété "message" du modèle 
                                $this->getModel()->setMessage( 'Inscription valid&eacute;e.' );     

                            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
                                $this->getModel()->setLogin($this->getSessionLogin());       

                            // Définition de la vue
                                $this->setView(getViewByName("Message"));

                        } else {

                            // Définition de la variable "message"
                                $message = $reponseDB;

                            // Appel de la fonction "createSignUpFormView" pour définir un modèle et une vue affichant le message
                                $this->createSignUpFormView($message);
                        }

                } else {
                    
                    // Définition de la variable "message"
                        $message = 'Le mot de passe et sa confirmation sont différents.';
                     
                    // Appel de la fonction "createSignUpFormView" pour définir un modèle et une vue affichant le message
                        $this->createSignUpFormView($message);
                }
	}

        
	private function createSignUpFormView($message) {
            
            // Définition du modèle
		$this->setModel(new MessageModel());
             
            // Affectation d'un message d'erreur à la propriété "message" du modèle 
		$this->getModel()->setMessage($message);
            
            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
		$this->getModel()->setLogin($this->getSessionLogin());            
                
            // Définition de la vue
		$this->setView(getViewByName("SignUpForm"));
	}

}


?>
