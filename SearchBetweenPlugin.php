<?php

/**
 * SearchBetween
 * 
 * @copyright Copyright © 2015 Richard Doe
 * @license http://opensource.org/licenses/MIT MIT
 */

/**
 * The SearchBetween plugin.
 * 
 * @package Omeka\Plugins\SearchBetween
 */
class SearchBetweenPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'config_form',
        'config',
        'public_items_search',
        'items_browse_sql',
    );
    
    public function hookInstall()
    {
        set_option('search_between_lower_element_id', '');
        set_option('search_between_upper_element_id', '');
        set_option('search_between_form_label', '');
    }

    public function hookUninstall()
    {
        delete_option('search_between_lower_element_id');
        delete_option('search_between_upper_element_id');
        delete_option('search_between_form_label');
    }
    
    public function hookConfigForm()
    {
        $elementOptions = $this->_getOptionsForElementSelect();
        include 'config_form.php';
    }
    
    public function hookConfig($args)
    {
        set_option('search_between_lower_element_id', $_POST['search_between_lower_element_id']);
        set_option('search_between_upper_element_id', $_POST['search_between_upper_element_id']);
        set_option('search_between_form_label', $_POST['search_between_form_label']);
    }
    
    public function hookPublicItemsSearch($args)
    {
        $view = $args['view'];
        echo $view->partial('search-between/advanced-search-partial.php');
    }
    
    public function hookItemsBrowseSql($args)
    {
        $fromText = isset($args['params']['search-between-from']) ? $args['params']['search-between-from'] : null;
        $toText = isset($args['params']['search-between-to']) ? $args['params']['search-between-to'] : null;
        
        if (empty($fromText) && empty($toText)) {
            return;
        }
        
        $lowerElementId = get_option('search_between_lower_element_id');
        $upperElementId = get_option('search_between_upper_element_id');
        
        if (empty($lowerElementId) && empty($upperElementId)) {
            return;
        }
        
        $db = $this->_db;
        $select = $args['select'];
        
        $fromSql = $db->quote($fromText);
        $toSql = $db->quote($toText);

        if (!empty($lowerElementId)) {
            $lowerElementTextAlias = 'search_between_lower_' . $db->getTable('ElementText')->getTableAlias();
            $select
                ->joinInner(
                    array($lowerElementTextAlias => $db->ElementText),
                    "$lowerElementTextAlias.record_id = items.id",
                    array()
                )
                ->where("$lowerElementTextAlias.record_type = 'Item'")
                ->where("$lowerElementTextAlias.element_id = ?", $lowerElementId);
        }
        
        if (!empty($upperElementId)) {
            $upperElementTextAlias = 'search_between_upper_' . $db->getTable('ElementText')->getTableAlias();
            $select
                ->joinInner(
                    array($upperElementTextAlias => $db->ElementText),
                    "$upperElementTextAlias.record_id = items.id",
                    array()
                )
                ->where("$upperElementTextAlias.record_type = 'Item'")
                ->where("$upperElementTextAlias.element_id = ?", $upperElementId);
        }
            
        if (empty($fromText)) {
            // only an upper boundary
            if (!empty($upperElementId)) {
                $select->where("$upperElementTextAlias.text <= $toSql");
            } 
            if (!empty($lowerElementId)) {
                $select->where("$lowerElementTextAlias.text <= $toSql");
            }
        } else if (empty($toText)) {
            // only a lower boundary
            if (!empty($upperElementId)) {
                $select->where("$upperElementTextAlias.text >= $fromSql");
            } 
            if (!empty($lowerElementId)) {
                $select->where("$lowerElementTextAlias.text >= $fromSql");
            }
        } else {
            // both lower and upper boundaries
            if (!empty($upperElementId) && !empty($lowerElementId)) {
                $select->where("(($lowerElementTextAlias.text >= $fromSql AND $lowerElementTextAlias.text <= $toSql) OR ($upperElementTextAlias.text >= $fromSql AND $upperElementTextAlias.text <= $toSql))");
            } else if (!empty($upperElementId)) {
                $select->where("($upperElementTextAlias.text >= $fromSql AND $upperElementTextAlias.text <= $toSql)");
            } else if (!empty($lowerElementId)) {
                $select->where("($lowerElementTextAlias.text >= $fromSql AND $lowerElementTextAlias.text <= $toSql)");
            }
        }
    }
    
    private function _getOptionsForElementSelect()
    {
        $elements = $this->_findElementsForSelect();
        $options = array('' => __('Select Below'));
        foreach ($elements as $element) {
            $optGroup = __($element['set_name']);
            $value = __($element['name']);
            $options[$optGroup][$element['id']] = $value;
        }
        return $options;
    }
    
    private function _findElementsForSelect()
    {
        $db = get_db();
        $table = $db->getTable('Element');
        
        $select = $table->getSelect()
            ->order(array('element_sets.name', 'elements.name'));

        return $table->fetchAll($select);
    }
}

