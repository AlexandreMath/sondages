<div class="survey">

    <div class="question">
        <?php echo $survey->getQuestion() ?>
    </div>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?action=Vote" >
        <?php foreach ($survey->getResponses() as $response) { ?>
            <input class="field"  name="responseId" type="radio" id="<?php echo $response -> getId(); ?>" value="<?php echo $response -> getId(); ?>" />
            <label class="label" for="<?php echo $response -> getId(); ?>"><?php echo $response -> getTitle().' - '.$response->getPercentage().'&percnt;'; ?></label></br>
    
        <?php } ?>
        
        <input class="submit" name="voter" type="submit" value="Voter" />
    </form>
</div>

