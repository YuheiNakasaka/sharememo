    //名前で画像の差込み場所を変更
    var ext = ".jpg";
    function handleFileSelect(evt) {
        var files = evt.target.files;
        for (var i = 0, f; f = files[i]; i++) {
            switch(f.name){
                case "top" + ext:
                    _pushImg("prev_top",f);
                break;
                case "rensai1" + ext:
                    _pushImg("prev_rensai_left",f);
                break;
                case "rensai2" + ext:
                    _pushImg("prev_rensai_center",f);
                break;
                case "rensai3" + ext:
                    _pushImg("prev_rensai_right",f);
                break;
                case "last" + ext:
                    _pushImg("prev_last",f);
                break;
                default:
                break;
            }
        }
    }
    //画像をtargetに追加
    function _pushImg(targetId,targetfile){
        var fr = new FileReader();
        var img = document.createElement('img');
        fr.onload = function(){
            img.src = this.result;
            //すでに画像があればリセット
            var _d = document.getElementById(targetId);
            var childs = _d.childNodes;
            if(childs.length != 0){
                _d.removeChild(childs[0]);
            }
            //差込
            _d.appendChild(img);
        }
        fr.readAsDataURL(targetfile);
    }
    //画像が追加されたら発火
    document.getElementById('files').addEventListener('change',handleFileSelect,false);
