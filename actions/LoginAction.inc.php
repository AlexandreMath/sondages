<?php

require_once("models/Model.inc.php");
require_once("actions/Action.inc.php");

class LoginAction extends Action {

	/**
	 * Traite les données envoyées par le visiteur via le formulaire de connexion
	 * (variables $_POST['nickname'] et $_POST['password']).
	 * Le mot de passe est vérifié en utilisant les méthodes de la classe Database.
	 * Si le mot de passe n'est pas correct, on affecte la chaîne "erreur"
	 * à la variable $loginError du modèle. Si la vérification est réussie,
	 * le pseudo est affecté à la variable de session et au modèle.
	 *
	 * @see Action::run()
	 */
	public function run() {
            
            // On vérifie dans la DB que le couple "nickname-password" est correct
                if ( $this -> database -> checkPassword( $_POST['nickname'], $_POST['password'] )) {
                    
                    // Définition du modèle 
                        $this -> setModel( new Model() );
                    
                    // Affectation du "nickname" entré par l'utilisateur à la propriété "login" du modèle et à la variable de session "login"
                        $this -> getModel() -> setLogin( $_POST['nickname']);
                        $this -> setSessionLogin( $_POST ['nickname']);

                } else {
                    
                    // Définition du modèle 
                        $this -> setModel( new Model() );
                    
                    // Affectation d'un message d'erreur à la propriété "loginError" du modèle
                        $this -> getModel() -> setLoginError('Erreur');

                }
            
            // Définition de la vue
                $this -> setView( getViewByName('default') );
            
        }

}

?>
