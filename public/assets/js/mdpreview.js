    //inputされた文字をリアルタイムプレビュー
    function showPreview(from,to){
        var text = document.getElementById(from).value;
        //改行を適用する
        //text = text.replace(/[\r\n|\n|\r]/g,'<br/>');
        text = marked(text);
        document.getElementById(to).innerHTML  = (text);
    }
    //保存された文字をリアルタイムプレビュー
    function displayMemo(from,to){
        var text = document.getElementById(from).innerHTML;
        //改行を適用する
        //text = text.replace(/[\r\n|\n|\r]/g,'<br/>');
        text = marked(text);
        document.getElementById(to).innerHTML  = (text);
    }
    window.onload = function(){
        displayMemo('md-display','md-display');
    }