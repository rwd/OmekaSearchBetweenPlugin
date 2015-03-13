<?php

$request = Zend_Controller_Front::getInstance()->getRequest();

// Get the field values from parameters
$searchFrom = trim($request->getParam('search-between-from'));
$searchTo = trim($request->getParam('search-between-to'));

?>

<fieldset>
    <legend><?php echo __(get_option('search_between_form_label')); ?></legend>
    
    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('search-between-from', __('From')); ?>
        </div>
        <div class="five columns omega inputs">
            <?php echo $this->formText('search-between-from',  $searchFrom, array('size' => '40', 'class' => 'search-between search-between-from')); ?>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo $this->formLabel('search-between-to', __('To')); ?>
        </div>
        <div class="five columns omega inputs">
            <?php echo $this->formText('search-between-to',  $searchTo, array('size' => '40', 'class' => 'search-between search-between-to')); ?>
        </div>
    </div>
</fieldset>
