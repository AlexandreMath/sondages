<?php

require_once("models/MessageModel.inc.php");
require_once("actions/Action.inc.php");

class UpdateUserAction extends Action {

	/**
	 * Met à jour le mot de passe de l'utilisateur en procédant de la façon suivante :
	 *
	 * Si toutes les données du formulaire de modification de profil ont été postées
	 * ($_POST['updatePassword'] et $_POST['updatePassword2']), on vérifie que
	 * le mot de passe et la confirmation sont identiques.
	 * S'ils le sont, on modifie le compte avec les méthodes de la classe 'Database'.
	 *
	 * Si une erreur se produit, le formulaire de modification de mot de passe
	 * est affiché à nouveau avec un message d'erreur.
	 *
	 * Si aucune erreur n'est détectée, le message 'Modification enregistrée.'
	 * est affiché à l'utilisateur.
	 *
	 * @see Action::run()
	 */
	public function run() {
      
            // Récupération des données entrées dans le formulaire de modification
                $password = $_POST['updatePassword'];            
                $password2 = $_POST['updatePassword2'];     
            
            // On vérifie que toutes les données ont bien été entrées dans le formulaire de modification
                if ( isset($password) && isset($password2) && trim( $password ) !='' && trim ($password2) != '' ) {
                    
                    // On vérifie que les mots de passe entrés sont identiques
                        if ( $password == $password2 ) {
                            
                            // On tente de modifier le mot de passe comme demandé dans la DB
                                $reponseDB = $this -> database -> updateUser( $this -> getSessionLogin() , $password );
                            
                            // En fonction de la réussite ou non de la modif, on définit un modèle et une vue affichant un message
                                if ($reponseDB === true) {
                                    
                                    // Définition du modèle
                                        $this->setModel(new MessageModel());
                                    
                                    // Affectation d'un message de réussite à la propriété "message" du modèle
                                        $this->getModel()->setMessage( 'Modification enregistrée.' );

                                    // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
                                        $this->getModel()->setLogin($this->getSessionLogin());  
                                    
                                    // Définition de la vue 
                                        $this->setView(getViewByName("Message")); 
                                        
                                } else {
                                    // Définition de la variable "message"
                                        $message = $reponseDB;
                        
                                    // Appel de la fonction "createUpdateUserFormView" pour définir un modèle et une vue affichant le message
                                        $this->createUpdateUserFormView($message);    
                                }

                        } else {
                            // Définition de la variable "message"
                                $message = 'Le mot de passe et sa confirmation sont différents.';
                        
                            // Appel de la fonction "createUpdateUserFormView" pour définir un modèle et une vue affichant le message
                                $this->createUpdateUserFormView($message);   
                        }

                } else {
                    // Définition de la variable "message"
                        $message = 'Les données n\'ont pas été entrées correctement.';
                        
                    // Appel de la fonction "createUpdateUserFormView" pour définir un modèle et une vue affichant le message
                        $this->createUpdateUserFormView($message);             

                }

            
	}

	private function createUpdateUserFormView($message) {
            
            // Définition du modèle
		$this->setModel(new MessageModel());
     
            // Affectation d'un message d'erreur à la propriété "message" du modèle 
		$this->getModel()->setMessage($message);
            
            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
		$this->getModel()->setLogin($this->getSessionLogin());  
                
            // Définition de la vue
		$this->setView(getViewByName("UpdateUserForm"));
	}

}

?>
