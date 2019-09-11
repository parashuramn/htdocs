<?php
function modify( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
        global $modifier, $merged_modifiers;

        $idx = _modifier_build_unique_id($tag, $function_to_add, $priority);
        $modifier[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
        unset( $merged_modifiers[ $tag ] );
        return true;
}

function modified( $tag, $value = "") {
        global $modifier, $merged_modifiers, $current_modifier;

        $args = array();

        if ( !isset($modifier[$tag]) ) {
                if ( isset($modifier['all']) )
                        array_pop($current_modifier);
                return $value;
        }

        if ( !isset($modifier['all']) )
                $current_modifier[] = $tag;

        // Sort
        if ( !isset( $merged_modifiers[ $tag ] ) ) {
                ksort($modifier[$tag]);
                $merged_modifiers[ $tag ] = true;
        }

        reset( $modifier[ $tag ] );

        if ( empty($args) )
                $args = func_get_args();

        do {
                foreach( (array) current($modifier[$tag]) as $the_ )
                        if ( !is_null($the_['function']) ){
                                $args[1] = $value;
                                $value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
                        }

        } while ( next($modifier[$tag]) !== false );

        array_pop( $current_modifier );

        return $value;
}
function _modifier_build_unique_id($tag, $function, $priority) {
        global $modifier;
        static $modifier_id_count = 0;

        if ( is_string($function) )
                return $function;

        if ( is_object($function) ) {
                // Closures are currently implemented as objects
                $function = array( $function, '' );
        } else {
                $function = (array) $function;
        }

        if (is_object($function[0]) ) {
                // Object Class Calling
                if ( function_exists('spl_object_hash') ) {
                        return spl_object_hash($function[0]) . $function[1];
                } else {
                        $obj_idx = get_class($function[0]).$function[1];
                        if ( !isset($function[0]->modifier_id) ) {
                                if ( false === $priority )
                                        return false;
                                $obj_idx .= isset($modifier[$tag][$priority]) ? count((array)$modifier[$tag][$priority]) : $modifier_id_count;
                                $function[0]->modifier_id = $modifier_id_count;
                                ++$modifier_id_count;
                        } else {
                                $obj_idx .= $function[0]->modifier_id;
                        }

                        return $obj_idx;
                }
        } else if ( is_string($function[0]) ) {
                // Static Calling
                return $function[0] . '::' . $function[1];
        }
}