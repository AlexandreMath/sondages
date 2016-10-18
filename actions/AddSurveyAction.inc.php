<?php

require_once("models/MessageModel.inc.php");
require_once("models/Survey.inc.php");
require_once("models/Response.inc.php");
require_once("actions/Action.inc.php");

class AddSurveyAction extends Action {

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
            
            // Récupération des données entrées dans le formulaire permettant d'ajouter un sondage
                $question = $_POST['questionSurvey']; 

                
                $tabReponses = array();
            
                for ($i = 1; $i <= 5; $i++) {
                    if ( isset ($_POST['responseSurvey'.$i ] ) && trim ($_POST['responseSurvey'.$i ]) != ''){
                        array_push($tabReponses, $_POST['responseSurvey'.$i ] );
                    }
                }

                               

            // On vérifie que la question a bien été entrée dans le formulaire d'ajout de sondage   
                if ( isset($question) && trim ($question) != '' ) {
               
                    // On vérifie qu'au moins deux réponses ont été entrées dans le formulaire d'ajout de sondage
                        if ( sizeOf( $tabReponses ) >= 2 ) {

                            // On instancie un objet de type "Survey"
                                $survey = new Survey( $this -> getSessionLogin(), $question );

                            // Pour chaque réponse contenue dans le tableau "tabReponses", on instancie un objet de type "Response"
                                for ( $i = 0; $i < sizeOf ($tabReponses); $i++) {
                                    $response = new Response ( $survey, $tabReponses[$i] ); 

                                    // On ajoute la réponse au sondage
                                        $survey -> addResponse( $response );
                                }

                            // On tente d'ajouter le sondage dans la DB
                                $reponseDB = $this -> database -> saveSurvey( $survey );

                            // En fonction de la réussite ou non de l'ajout, on définit un modèle et une vue affichant un message    
                                if ( $reponseDB === true ) {
                                    
                                    // Définition du modèle
                                        $this->setModel(new MessageModel());

                                    // Affectation d'un message de réussite à la propriété "message" du modèle 
                                        $this->getModel()->setMessage('Merci, nous avons ajouté votre sondage.');
                                
                                    // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
                                         $this->getModel()->setLogin($this->getSessionLogin());  
                                    
                                    // Définition de la vue
                                        $this->setView(getViewByName("addsurveyform"));                          

                                } else {
                                    
                                    // Définition du modèle
                                        $this->setModel(new MessageModel());

                                    // Affectation d'un message d'erreur à la propriété "message" du modèle 
                                        $this->getModel()->setMessage( $reponseDB );
                                
                                    // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
                                         $this->getModel()->setLogin($this->getSessionLogin());  
                                    
                                    // Définition de la vue
                                        $this->setView(getViewByName("addsurveyform"));                               
                                }

                        } else {
                            
                            // Définition du modèle
                                $this->setModel(new MessageModel());
                             
                            // Affectation d'un message d'erreur à la propriété "message" du modèle 
                                $this->getModel()->setMessage('Il faut saisir au moins 2 réponses.');
                                
                            // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
                                $this->getModel()->setLogin($this->getSessionLogin());  

                            // Définition de la vue
                                $this->setView(getViewByName("addsurveyform"));       
                                
                        }

                } else {

                    // Définition du modèle
                        $this->setModel(new MessageModel());

                    // Affectation d'un message d'erreur à la propriété "message" du modèle 
                        $this->getModel()->setMessage('La question est obligatoire.');

                    // Affectation de la valeur de la variable de session "login" à la propriété "login" du modèle
                        $this->getModel()->setLogin($this->getSessionLogin());  

                    // Définition de la vue
                        $this->setView(getViewByName("addsurveyform"));                  
                }
           
            
            
	}

}

?>
