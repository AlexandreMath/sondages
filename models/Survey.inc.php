<?php
class Survey {
	
	private $id;
	private $owner;
	private $question;
	private $responses;

	public function __construct($owner, $question) {
		$this->id = null;
		$this->owner = $owner;
		$this->question = $question;
		$this->responses = array();
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function addResponse($response) {
		$this->responses[] = $response;
	}

	public function getId() {
		return $this->id;
	}

	public function getOwner() {
		return $this->owner;
	}
	
	public function getQuestion() {	
		return $this->question;
	}

	public function getResponses() {
		return $this->responses;
	}
	
	public function computePercentages() {
            // On compte le nombre total de vote, toute réponses du sondage comprises
                $total = 0;
            
                for ( $i = 0; $i < count($this->responses); $i++){
                    $total +=  $this->responses[$i]->getCount();
                }
            
            // On définit le pourcentage de chaque réponse
               for ( $i = 0; $i < count($this->responses); $i++){
                   $this->responses[$i]->computePercentage($total);
               }
            
	}

}
?>
