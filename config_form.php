<?php $view = get_view(); ?>
<fieldset>
    <div class="field">
        <div class="two columns alpha">
            <label for="search_between_lower_element_id" class="required"><?php echo __('Lower boundary element'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"></p>
            <?php echo $view->formSelect('search_between_lower_element_id', get_option('search_between_lower_element_id'), array(), $elementOptions); ?>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <label for="search_between_upper_element_id"><?php echo __('Upper boundary element'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"></p>
            <?php echo $view->formSelect('search_between_upper_element_id', get_option('search_between_upper_element_id'), array(), $elementOptions); ?>
        </div>
    </div>
    
    <div class="field">
        <div class="two columns alpha">
            <label for="search_between_form_label"><?php echo __('Form label'); ?></label>
        </div>
        <div class="inputs five columns omega">
            <p class="explanation"></p>
            <?php echo $view->formText('search_between_form_label', get_option('search_between_form_label')); ?>
        </div>
    </div>
</fieldset>
