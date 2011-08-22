$(document).ready(function(){
	
	$talker_client.load();
	
});


function talker_open(){ 
	window.frames['_easy_talker_iframe'].$talker.open(); 
}

var $talker_client = {
	url:"http://talk.easywebmanager.com/",
	load:function(){
		
		$('body').append("<div id='_easy_talker_baloon' style='-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;-webkit-box-shadow: 0px 0px 5px 1px #000000;-moz-box-shadow: 0px 0px 5px 1px #000000;box-shadow: 0px 0px 5px 1px #000000;display:none;position:fixed;z-index:50;top:50px;right:45px;width:150px;max-height:50px;overflow:auto;text-align:center;font-size:16px;background:#fff;cursor:pointer;color:#000;font-size:11px;font-family:Arial;padding:10px;'></div><div id='_easy_talker' style='-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;-webkit-box-shadow: 0px 0px 5px 1px #000000;-moz-box-shadow: 0px 0px 5px 1px #000000;box-shadow: 0px 0px 5px 1px #000000;position:fixed;z-index:50;top:50px;right:0px;width:40px;height:45px;background:#000;cursor:pointer;'></div>");
		$('#_easy_talker').click(function(){ window.frames['_easy_talker_iframe'].$talker.open(); });
		$('#_easy_talker_baloon').click(function(){ window.frames['_easy_talker_iframe'].$talker.open(); });
		
		$('body').append("<div id='_easy_talker_iframe_c' style='-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;-webkit-box-shadow: 0px 0px 5px 3px #000000;-moz-box-shadow: 0px 0px 5px 3px #000000;box-shadow: 0px 0px 5px 3px #000000;display:none;position:fixed;z-index:51;top:50px;right:0px;width:250px;height:350px;background:#000;cursor:pointer;'>" +
							"<iframe src='about:blank' id='_easy_talker_iframe' name='_easy_talker_iframe' style='background-color: transparent; vertical-align: text-bottom; overflow: hidden; position: relative; width: 100%; height: 100%; margin: 0pt;border:none;' frameborder='0' allowtransparency='true'></iframe>" +
						 "</div>");
		
	    //var myIFrame = document.getElementById("_easy_talker_iframe").contentWindow.document;
		var myIFrame = window.frames["_easy_talker_iframe"].document;
	    
	    var content = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'+
	    				'<head>'+
	    				'<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'+
	    				'<script type="text/javascript" src="' + this.url + 'client/cookie.js"></'+'script>'+
	    				'<script type="text/javascript" src="' + this.url + 'client/jquery.js"></'+'script>'+
	    				'<script type="text/javascript" src="' + this.url + 'client/jquery-ui.js"><'+'/script>'+
	    				'<script type="text/javascript" src="' + this.url + 'client/talker.js"></'+'script>'+
	    				'<body onload="javascript: $talker.start('+_SITE_ID_+');"></body>'+
	    				'</html>';
	    myIFrame.open();
	    myIFrame.write(content);
	    myIFrame.close();

	}
}
