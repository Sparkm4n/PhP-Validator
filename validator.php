<?php

$error=""; //general error var
$formatD=""; //var for datetime
$pattern=""; //pattern var for regex
$parameters="" //any additional parameters

    public function clean_n_validate($key, $val, $parameters)
    {
        global $error; global $formatD;
        switch($key){
            case "number":
                $point = str_replace(',', '.', $val);
                $val = filter_var($point, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                if(is_numeric($val)){
                    if(preg_match('/^\d+$/', $val)){
                        if(filter_var($val, FILTER_VALIDATE_INT) === 0 || filter_var($val, FILTER_VALIDATE_INT)){
                            return $val;
                        } else $error .= "The ".$val." is not a integer ";
                    }
                    elseif(preg_match('/^\d+\.\d+$/', $val)){
                        if(filter_var($val, FILTER_VALIDATE_FLOAT)){
                            return $val;
                        } else $error .= "The ".$val." is not a double ";
                    } else $error .= "not recognized ";
                } else $error .= "you didnt type in a number ";
            break;
            case "email":
                $val = filter_var($val, FILTER_SANITIZE_EMAIL);
                if(filter_var($val, FILTER_VALIDATE_EMAIL)){
                    return $val;
                } else $error .= "Email ".$val." is not valid ";
            break;
            case "tel":
                $val = filter_var($val, FILTER_SANITIZE_STRING);
                if(filter_var($val, FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^\+?([0-9\/ -]+)$/")))){
                    return $val;
                } else $error .= "Telefon/Fax Nr ".$val." is not valid ";
            break;
            case "kalender":
                $val = date($formatD, strtotime(str_replace('/', '-', filter_var($val, FILTER_SANITIZE_STRING)).$parameters)); 
                $res = validateDate($val, $formatD);
                if($res == TRUE){
                    return $val;
                } else $error .= "date/time Format ".$val." is invalid ";
            break;
            case "text":
                $val = filter_var($val, FILTER_SANITIZE_STRING);
                if(filter_var($val, FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>"/^[\d*\w*\s]+$/")))){	
                    return $val;
                } else $error .= "Text ".$val." is invalid ";
            break;
            case "pattern_defined":
                //pattern defined sanitizing
                global $pattern;			
                if(filter_var($val, FILTER_VALIDATE_REGEXP, array("options" => array("regexp"=>$pattern)))){
                    return $val;
                } else $error .= " defined ".$val." is not valid ";
                break;
            case "empty":
                return $val;
            break;
            default:
                $error .= " undefined ";
            break;
        }
    }
    function validateDate($date, $format = 'Y-m-d H:i')
    { //datetime validation
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    function mandatory($val, $elem)
    { //addtional mandatory check
        if($val == "required"){
            if(empty($elem)) return FALSE;	
        } else return TRUE;
    }
    function MinMax($min, $max, $elem)
    { //additional check of min max
        if(strlen($elem)>$max){
            return FALSE;
        } elseif(strlen($elem<$min)){
            return FALSE;
        } else return TRUE;
    }
