var $talker = {
	interval:20000,
	closed:true,
	parent_window:window.top,
	site_url:"http://vytautas/talker/",
	url:"server/index.php?a=",
	open:function(){
		this.parent_window.$('#_easy_talker_iframe_c').show(/*"slide",{direction: 'right'},*/500); this.parent_window.$('#_easy_talker').hide();
		this.closed = false;
		this.parent_window.$('#_easy_talker_baloon').hide();
	},
	close:function(){
		this.parent_window.$('#_easy_talker_iframe_c').hide(500); this.parent_window.$('#_easy_talker').show();
		this.closed = true;
	},
	sendMessage:function (site_id, visit_id){
		msg = $('#txt').val();
		var $obj = this;
		$.ajax({
			async: false,
			url: $obj.url + 'sendMessage&p[site_id]=' + $obj.site_id + '&p[visit_id]=' + $obj.visit_id + '&p[msg]=' + $obj.escape(msg),
			dataType:"json",
			success: function(conts) {
				$obj.update(conts);
				$('#txt').val('');
			}
		});
	},
	load:function(){
		var $obj = this;
		$.ajax({
			async: false,
			url: $obj.url + 'getNewMessages&p[site_id]='+$obj.site_id+'&p[visit_id]='+$obj.visit_id+'&p[time]=' + $obj.time,
			dataType:"json",
			success: function(conts) {
				if(conts.length > 0){
					for(j in conts){
						$obj.update(conts[j]);
					}
					$obj.time = conts[0].time;
					if($obj.closed){
						$obj.showBaloon(conts[0].message);
					}
				}
			}
		});
		call = "$talker.load()";
		setTimeout(call, $obj.interval);
	},
	showBaloon:function(msg){
		baloon = this.parent_window.$('#_easy_talker_baloon');
		baloon.html(msg);
		baloon.show();
	},
	escape:function(msg){
		return escape(msg);
	},
	update:function(conts){
		$("#messages .content").append("<div class='msg radius3 dir" + conts.direction + "'><em>" + conts.create_date + "</em><p>" + conts.message + "</p></div>");
	},
	registerVisit:function(){
		var $obj = this;
		$.ajax({
			async: false,
			url: $obj.url + 'registerVisitor&p[site_id]='+$obj.site_id+'&p[visit_id]='+getCookie('visit_id')+'&p[referer]='+$obj.escape(window.top.document.referrer),
			success: function(id) {
				$obj.visit_id = id;
				setCookie('visit_id', id);
			}
		});
	},
	detectVisit:function(){
		this.registerVisit();
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
		this.detectVisit();
		this.resetTime();
		loadcssfile(document, this.site_url + "client/style.css");
		$('body').append("<div id='main'>" +
				"<div id='close' onclick='javascript: $talker.close();'>close</div>" +
				"<div id='messages' class='radius5'><div class='content'></div></div>" +
				"<form id='form' action='javascript: void($talker.sendMessage());' >" +
				"<input type='text' id='txt' class='radius3' placeholder='Send text message' /> <input type='submit' id='btn' value='Send' class='radius3' />" +
				"</form></div>");
		$obj = this;
		this.load();
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