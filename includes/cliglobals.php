<?php

include "clicolours.php";
$CLICOLURS = new clicolours();

function ask($question, $type = null, $length = null)
{
    /*
    Prompt the user with a question, potentially with default answers  
    
    $type may be NULL, TRUE, FALSE, or a string or array containing an option list
    
    Option list syntax: {option1}|{option2}(|{option3}...)
    Option lists can also be passed as arrays: array({option1}, {option2}, ...)
    To make an option the default, prepend a single underscore (ie '_blue')
    If more than one option is so marked, only the first is used.
    
    If passed a string with no delimiters, or an array with a single entry, ask() will assume a 
    freeform question, with a default answer.
    
    Valid Option Lists:
    _r/g/b
    r/g/_b
    Michael/_Jim/_Bob #Jim would be default here, Bob would be treated as any other entry
    
    ask() is not case sensitive, and will not modify the case of responses. 
    
    In the case of binary, boolean responses, ask will return TRUE or FALSE, otherwise it will return
    the appropriate string
    */
    global $CLICOLURS;
    $default_response = null;
    $response = null;
    $valid_responses = array('y', 'n', 'Y', 'N');
    $correct_response = false;
    $boolean_answer = false;
    
    if ($type === null)
    {
        #default assumption is completely open ended question, no answer list, no default response
        $question .= ': ';
        $valid_responses = null;
    }
    elseif ($type === true)
    {
        #true assumes a yes or no question, with the default answer being yes
        $question .= ' ['.$CLICOLURS->getColouredString('Y','green').'/n]: ';
        $default_response = 'Y';
        $boolean_answer = true;
    }
    elseif ($type === false)
    {
        #false assumes a yes or no question, with the default answer being no
        $question .= ' [y/'.$CLICOLURS->getColouredString('N','red').']: ';
        $default_response = 'N';
        $boolean_answer = true;
    }
    elseif ((is_string($type) && (preg_match('%(([\d\w_]+)\|?)+%i', $type) === 1)) || (is_array($type)))
    {
        #was passed an option list, potentially with a default
        
        #if only passed a single option, assume freeform question, with option as default
        if ((is_string($type) && (strpos($type, '|') === false)) || (is_array($type) && (count($type) < 2)))
        {
            $valid_responses = null;
            $default_response = is_string($type) ? $type : $type[0];
            $default_response = trim_($default_response);
            $question .= ' [' . $CLICOLURS->getColouredString($default_response, 'green') . ']: ';
        }
        else #restrict responses to the list
        {
        
            #explode if not already array
            $valid_responses = is_string($type) ? explode('|', $type) : $type;
            
            #default response is the first valid response that starts with _ 
            $default_responses = preg_grep('%^_%', $valid_responses);
            $default_response  = array_shift($default_responses);
            
            #clean up the default and valid responses
            $default_response = trim_($default_response);
            $valid_responses = array_map('trim_', $valid_responses);
            
            #create separate array to colourize to simplify comparison later, colour default response green
            $default_response_offset = array_search($default_response, $valid_responses);
            $valid_responses_colourized = $valid_responses;
            $valid_responses_colourized[$default_response_offset] = $CLICOLURS->getColouredString($valid_responses[$default_response_offset], 'green');
            
            
            $question .= ' [' . implode('/',$valid_responses_colourized) . ']: ';
        }
    }
    else
    {
        #was passed fucked up data
        die ("you done fucked up!");
    }
    if (isset($valid_responses)) #not a freeform question
    {
        while (!$correct_response)
        {
            echo $question;
            
            #fgets doesnt like null $length
            $response = is_int($length) ? fgets(STDIN, $length) : fgets(STDIN);
            $response = trim($response); 
            
            #if the user just hit enter, use the default
            $response = (strlen($response) > 0) ? $response : $default_response;
            if (in_array($response, $valid_responses))
            {
                $correct_response = true;
            }
            else
            {
                echo "Invalid response, please try again\n";
            }
        }
    }
    else #freeform question
    {
        echo $question;
        
        #fgets doesnt like null $length
        $response = is_int($length) ? fgets(STDIN, $length) : fgets(STDIN);
        $response = trim($response); 
        #if the user just hit enter, use the default
        $response = (strlen($response) > 0) ? $response : $default_response;
    }
    
    #if the user entered something, return it, else return the default
    if ($boolean_answer)
    {
        return (strtolower($response) == 'y') ? true : false;
    }
    return $response;
}
?>