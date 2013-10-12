function onKeyDownInput(o, e)
{
    var kC = e.keyCode ? e.keyCode : e.charCode ? e.charCode : e.which;
    if(kC == 13 && !e.shiftKey && !e.ctrlKey && !e.altKey ){
        e.returnValue = false;
        return false;
    }
    return true;
}

function onKeyDown(o, e)
{
    var kC = e.keyCode ? e.keyCode : e.charCode ? e.charCode : e.which;
    if (kC == 9 && !e.shiftKey && !e.ctrlKey && !e.altKey)
    {
        var oS = o.scrollTop;
        if (o.setSelectionRange)
        {
            var sS = o.selectionStart;
            var sE = o.selectionEnd;
            o.value = o.value.substring(0, sS) + "\t" + o.value.substr(sE);
            o.setSelectionRange(sS + 1, sS + 1);
            o.focus();
        }
        else if (o.createTextRange)
        {
            document.selection.createRange().text = "\t";
            e.returnValue = false;
        }
        o.scrollTop = oS;
        if (e.preventDefault)
        {
            e.preventDefault();
        }
        return false;
    }else if(e.ctrlKey == true && kC == 83 && !e.shiftKey && !e.altKey){
        e.returnValue = false;
        document.getElementById('cm_submit').click();
        return false;
    }
    return true;
}
