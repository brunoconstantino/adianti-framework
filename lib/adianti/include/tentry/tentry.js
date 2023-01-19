function entryMask(field, event, mask)
{
    var value, i, character, returnString;
    value = field.value;
    if(document.all) // IE
    {
        keyCode = event.keyCode;
    }
    else if(document.layers) // Firefox
    {
        keyCode = event.which;
    }
    else
    {
        keyCode = event.which;
    }
    if (keyCode == 8 || event.keyCode == 9) // backspace e caps
    {
        return true;
    }
    
    returnString = '';
    var n=0;
    for(i=0; i<mask.length-1; i++)
    {
        maskChar  = mask.charAt(i);
        valueChar = value.charAt(n);
        if (i <= value.length)
        {
            if (((maskChar == "-")  || (maskChar == "_") || (maskChar == ".") || (maskChar == "/") ||
                 (maskChar == "\\") || (maskChar == ":") || (maskChar == "|") ||
                 (maskChar == "(")  || (maskChar == ")") || (maskChar == "[") || (maskChar == "]") ||
                 (maskChar == "{")  || (maskChar == "}")) & (maskChar!==valueChar))
            {
                returnString += maskChar; 
            }
            else
            {
                returnString += valueChar;
                n ++;
            }
        }
    }
    field.value = returnString;
    
    if (mask.charAt(i-1) == "9") // only numbers
    {
        return ((keyCode > 47) && (keyCode < 58)); // among 0 ane 9
    }
    else // any char 
    {
        return true;
    }
    return true;
}