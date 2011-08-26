jQuery.support.cors = true;

var $talker = {
	interval:20000,
	closed:true,
	parent_window:window.top,
	site_url:window.top._OWL_SERVER_,
	url:"server/index.php?a=",
	auroresponded:false,
	open:function(){
		this.parent_window.$('#_easy_talker_iframe_c').show(/*"slide",{direction: 'right'},*/500); this.parent_window.$('#_easy_talker').hide();
		this.closed = false;
		this.parent_window.$('#_easy_talker_baloon').hide();
		this.interval = 5000;
		setCookie('opened', (this.closed?0:1));
	},
	close:function(){
		this.parent_window.$('#_easy_talker_iframe_c').hide(500); this.parent_window.$('#_easy_talker').show();
		this.closed = true;
		this.interval = 20000;
		setCookie('opened', (this.closed?0:1));
	},
	sendMessage:function (site_id, visit_id){
		msg = $('#txt').val();
		if(!this.validate(msg)){
			this.messageAlert($lang.empty_message);
			return false;
		}
		var $obj = this;
		
		$.getJSON(
				$obj.url + 'sendMessage&p[site_id]=' + $obj.site_id + '&p[visit_id]=' + $obj.visit_id + '&p[msg]=' + $obj.escape(msg), 
				function(conts){
					$obj.update(conts);
					$obj.setOnlineStatus(conts.online);
					if(!$obj.online) $obj.autoResponder();
					$('#txt').val('');
				});

	},
	autoResponder:function(){
		if(!this.auroresponded){
			this.update({ message:$lang.auto_respond_text, create_date:'', name:'', direction:0 });
			this.auroresponded = true;
		}
	},
	validate:function(msg){
		return (msg!=''?true:false);
	},
	messageAlert:function(text){
		alert(text);
	},
	loadHistory:function(){
		var $obj = this;
		$.getJSON(
				$obj.url + 'getOldMessages&p[site_id]='+$obj.site_id+'&p[visit_id]='+getCookie('visit_id'), 
				function(conts){
					$obj.setOnlineStatus(conts[0].online);
					if(conts.length > 1){
						for(j=1; j<conts.length; j++){
							$obj.update(conts[j]);
						}
						if($obj.closed){
							$obj.showBaloon(conts[1].message);
						}
					}
				});
	},
	load:function(){
		var $obj = this;
		$.getJSON(
				$obj.url + "getNewMessages&p[site_id]="+$obj.site_id+"&p[visit_id]="+$obj.visit_id+"&p[time]=" + $obj.time, 
				function(conts){
					$obj.time = conts[0].time;
					$obj.setOnlineStatus(conts[0].online);
					if(conts.length > 1){
						for(j=1; j<conts.length; j++){
							$obj.update(conts[j]);
						}
						if($obj.closed){
							$obj.showBaloon(conts[1].message);
						}
					}
				});
		call = "$talker.load()";
		setTimeout(call, $obj.interval);
	},
	setOnlineStatus:function(status){
		button = this.parent_window.$('#_easy_talker');
		if(status){
			button.css({'background':"url('"+this.site_url+"client/online.png') center center no-repeat"});
			this.online = true;
		}else{
			button.css({'background':"url('"+this.site_url+"client/offline.png') center center no-repeat"});
			this.online = false;
		}
	},
	showBaloon:function(msg){
		baloon = this.parent_window.$('#_easy_talker_baloon');
		baloon.html(msg);
		baloon.show();
	},
	escape:function(msg){
		return msg;
	},
	update:function(conts){
		$("#messages .content").append("<div class='msg radius3 dir" + conts.direction + "'><em>" + conts.create_date + "</em> <u>" + conts.name + "</u><p>" + conts.message + "</p></div>");
		$("#messages").animate({ scrollTop: $("#messages").attr("scrollHeight") }, 3000);
	},
	registerVisit:function(){
		var $obj = this;
		$.getJSON(
				$obj.url + 'registerVisitor&p[site_id]='+$obj.site_id+'&p[visit_id]='+getCookie('visit_id')+'&p[referer]='+$obj.escape(window.top.document.referrer)+'&p[url]='+$obj.escape(window.top.location), 
				function(id){
					$obj.visit_id = id;
					setCookie('visit_id', id);
					$obj.loadHistory();
					$obj.load();
				});
	},
	resetTime:function(){
		var $obj = this;
		$.ajax({
			async: false,
			url: $obj.url + 'getTime',
			success: function(date) {
				$obj.time = date;
			}
		});		
	},
	start:function(site_id){
		this.site_id = site_id;
		this.url = this.site_url + this.url;
		this.registerVisit();
		this.resetTime();
		loadcssfile(document, this.site_url + "client/style.css");
		$('body').append("<div id='main'>" +
				"<div id='close' onclick='javascript: $talker.close();'>" + $lang.close + "</div>" +
				"<div id='messages' class='radius5'><div class='content'></div></div>" +
				"<form id='form' action='javascript: void($talker.sendMessage());' >" +
				"<input type='text' id='txt' class='radius3' placeholder='" + $lang.txt_placeholder + "' /> <input type='submit' id='btn' value='" + $lang.msg_submit_btn + "' class='radius3' />" +
				"</form></div>");
		$obj = this;
		if(getCookie('opened')==1){
			this.open();
		}
	}
}




function loadjsfile(doc, filename){
	  var fileref=doc.createElement('script');
	  fileref.setAttribute("type","text/javascript");
	  fileref.setAttribute("src", filename);
	  doc.getElementsByTagName("head")[0].appendChild(fileref)
}
function loadcssfile(doc, filename){
	  var fileref=doc.createElement("link");
	  fileref.setAttribute("rel", "stylesheet");
	  fileref.setAttribute("type", "text/css");
	  fileref.setAttribute("href", filename);
	  doc.getElementsByTagName("head")[0].appendChild(fileref)
}
