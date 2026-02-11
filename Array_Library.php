<?php
/**
 * Open Source GPLv3
 */
declare(strict_types=1);
namespace iZiTA
{
    //<editor-fold desc="Initialization Process">
    //<editor-fold desc="Check Startup">
    $included_files = False;
    ((__FILE__ ?? $included_files = True) === (get_included_files()[0] ?? $included_files = True)) ? True : ($included_files === False ? False : True) and exit;
    //</editor-fold>
    date_default_timezone_set('UTC');
    defined('iZiTA>Array_Library') or die;
    //</editor-fold>
    /**
     * iZiTA::Array_Library<br>
     * Script version: <b>202602.0.0.25</b><br>
     * PHP Version: <b>8.5</b><br>
     * <b>Info</b>:<br>
     * iZiTA::Array_Library is an Array converting, checking library.<br>
     * @package iZiTA::Array_Library
     * @author : TheTimeAuthority
     */
    Final Class Array_Library
    {
        //<editor-fold desc="Final Array Functions">
        /**
         * Recursively get all array elements and values and return them as a string.
         * @param array $Array <p> The array to process.
         * @param String $Separator [optional]<p>
         * The separator to use between elements.<br>
         * Defaults to an empty string.</p>
         * @return String a string containing a string representation of all the array
         * elements in the same order, with the glue string between each element.
         */
        Final Function Array_To_String(array $Array, String $Separator = ''): String
        {
            $Array_To_String = ($this->Array_Recursively_Get_Flat($Array, $Separator) ?? '') ?: '';
            if(is_string($Array_To_String) === True)
            {
                return $Array_To_String;
            }
            return '';
        }
        /**
         * Return arrays last elements as flat array.
         * @param array $Array<p> The array to process.</p>
         * @param Int $Max_Depth<p> The Maximum Depth to dive inside the array.<br>
         * <i>> Default to 5.</i></p>
         * @param Bool $Only_From_MaxDepth<p> Only get the elements of the specified last depth.<br>
         * <i>> Defaults to True.</i></p>
         * @return array Returns an array of the last elements.
         */
        Final Function Array_Get_Last(array $Array, Int $MaxDepth = 5, Bool $Only_From_MaxDepth = False, Bool $Return_Max_With_Dimension = False, Bool $Flat_Un_dimensionalize = False, Bool $F_U_d_recursion_include_path = False, Bool $F_U_d_make_empty = False, Bool $Verification = False): array
        {
            $Array_To_Last = ($this->Array_Recursively_Make_Flat_On_Go($Array, MaxDepth: $MaxDepth, Only_From_MaxDepth: $Only_From_MaxDepth, Return_Max_With_Dimension: $Return_Max_With_Dimension,  Flat_Un_dimensionalize: $Flat_Un_dimensionalize, F_U_d_recursion_include_path: $F_U_d_recursion_include_path, F_U_d_make_empty: $F_U_d_make_empty, Verification: $Verification) ?? '') ?: '';
            if(is_array($Array_To_Last) === True)
            {
                return $Array_To_Last;
            }
            return [];
        }
        //</editor-fold>
        //<editor-fold desc="Private Array Functions">
        /**
         * Recursively get all array elements and values.<br>
         * @param array $Array <p> The array to process</p>
         * @param String $Separator [optional]<p>
         *  The separator to be used between elements.<br>
         *  <i>> Defaults to an empty string.</i></p>
         * @param bool $As_String <p> Indicates if the function will be parsed and returned as string or array.<br>
         *  <i>> Defaults to True.</i></p>
         * @param String $Prefix <p>
         * This is a prefix that is set by the function when calling itself to result in the full array element path.<br>
         * <i>> Defaults to an empty string. Do not change.</i></p>
         * @return array|String
         */
        Private Function Array_Recursively_Get_Flat(array $Array, String $Separator = '', Bool $As_String = True, String $Prefix = ''): array|String
        {#
            if($As_String === True)
            {# Return the result as string
                $Result = '';
            }else
            {# Return the result as array
                $Result = [];
            }
            $Prefix = (String)$Prefix;
            $Array_Keys = array_keys($Array);
            $Count = count($Array_Keys);
            foreach($Array_Keys as $K => $Key)
            {# Process each element of the array
                $Value = $Array[$Key];
                $Prefix_Length = strlen($Prefix);
                if($Prefix_Length > 0)
                {# Build the current key path //
                    $Current_Key = $Prefix.$Separator.(String)$Key;
                }else
                {# Build the current key path
                    $Current_Key = (String)$Key;
                }
                $isLast = ($K === $Count - 1);
                if(is_array($Value) === True)
                {# Recursively process nested arrays.
                    if($As_String === True)
                    {# Flatten the result when requested as a string
                        $nestedResult = $this->Array_Recursively_Get_Flat($Value, $Separator, True, $Current_Key);
                        if(is_array($nestedResult) === True)
                        {# On error empty nesterResult
                            $nestedResult = '';
                        }
                        $Result .= $nestedResult;
                        if($isLast === False)
                        {
                            $Result .= $Separator;
                        }
                    }else
                    {
                        $nestedResult = $this->Array_Recursively_Get_Flat($Value, $Separator, False, $Current_Key);
                        $Result[$Key] = $nestedResult;
                    }
                }else
                {# Handle non-array values.
                    if($As_String === True)
                    {
                        if(strlen($Result) > 0)
                        {
                            $Result .= $Separator;
                        }
                        $Result .= (String)$Current_Key;
                        if(empty($Value) === False)
                        {
                            $Result .= $Separator.(String)$Value;
                        }
                    }else
                    {# Preserve the key-value pair if not in string mode.
                        $Result[$Key] = $Value;
                    }
                }
            }
            return $Result;
        }
        /**
         * Recursively make array from and until (return flat until $MaxDepth) specified depth.
         * @param array $Array<p> The array to process.</p>
         * @param array $Result <b>(Leave empty)</b>
         * @param Int $Depth <b>(Leave empty)</b><p> Is the depth you are inside the array.</p>
         * @param Int $MaxDepth (Defaults to <b>5.</b>)<p> Is the maximum depth allowed to dive inside the array.<br>
         * @param Bool $Only_From_MaxDepth (Defaults to <b>False</b>.)<p> Only get elements as last elements if it's from specified Max Depth.</p>
         * @param Bool $Return_Max_With_Dimension (Defaults to <b>False</b>.)<p> When maximum depth is reached and more dimensions exist return them as last element.</p>
         * @param Bool $Flat_Un_dimensionalize (Defaults to <b>False</b>.)<p> Until maximum depth is reached make dimensions(array) flat array.</p>
         * @param Bool $F_U_d_recursion_include_path (Defaults to <b>False</b>.)<p> Include full previous path in each dimension.</p>
         * @param Bool $F_U_d_make_empty (Defaults to <b>False</b>.)<p> If the element is empty include it.</p>
         * TODO:@param String $Dynamic_Execution_Control <p> Define some rules to manage dimensions and how the function works for some depths.</p>
         * @param Bool $Verification (Defaults to <b>False</b>.)<p> If set to TRUE the last element of the flat array will be a SHA3-256 string of the array values.</p>
         * @return array Returns an array or a multidimensional array of the last elements or an empty array on failure.
         */
        Private Function Array_Recursively_Make_Flat_On_Go(array $Array, array &$Result = [], Int $Depth = 0, Int $MaxDepth = 5, Bool $Only_From_MaxDepth = False, Bool $Return_Max_With_Dimension = False, Bool $Flat_Un_dimensionalize = False, Bool $F_U_d_recursion_include_path = False, Bool $F_U_d_make_empty = False, Bool $Verification = False, Array $FUDArray = []): array
        {
            $Depth+=1;
            if($Depth > $MaxDepth)
            {
                return [''];
            }
            foreach($Array as $Index=>$Entry)
            {
                $Previous_Array = $FUDArray;
                $Previous_Array[] = $Index;
                if(is_array($Entry) === True)
                {
                    if($Depth != $MaxDepth)
                    {
                        if($Flat_Un_dimensionalize === True)
                        {
                            if(($F_U_d_recursion_include_path === False) and (empty($Index) === False or $F_U_d_make_empty === True))
                            {
                                $Result[] = (string)$Index;
                            }
                        }
                        $this->Array_Recursively_Make_Flat_On_Go($Entry, $Result, $Depth, $MaxDepth, $Only_From_MaxDepth, $Return_Max_With_Dimension, $Flat_Un_dimensionalize, $F_U_d_recursion_include_path, $F_U_d_make_empty, $Verification, $Previous_Array);
                    }elseif($Return_Max_With_Dimension === True)
                    {
                        $Result[] = $Entry;
                    }elseif($Flat_Un_dimensionalize === True)
                    {
                        if((key($Entry) ?? False) !== False or $F_U_d_make_empty === True)
                        {
                            $Result[] = ((string)key($Entry) ?? '');
                        }
                    }
                }else
                {
                    if($Flat_Un_dimensionalize === True and $F_U_d_recursion_include_path === True)
                    {
                        foreach($Previous_Array as $F_U_d_include)
                        {
                            $Result[] = $F_U_d_include;
                        }
                    }
                    if(($Only_From_MaxDepth === True and $Depth === $MaxDepth) or $Only_From_MaxDepth === False or $Flat_Un_dimensionalize === True)
                    {
                        $Result[] = $Entry;
                    }
                }
            }
            if($Verification === True)
            {
                $a = serialize($Result);
                $Result[] = hash('sha3-256', $a);
                $a = null;
                unset($a);
            }
            return $Result;
        }
        //</editor-fold>
    }
}?>
