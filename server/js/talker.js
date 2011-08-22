$(document).ready(function () {
	//setTimeout("$talker.load()", $talker.interval);

	$server.loadSites();
	
	openTab($_sites[0].id);

	$('input,textarea,select').change(function(){ $(this).removeClass('err'); });

});


function createSites($sites){
	for(var i=0; i<$sites.length; i++){
		$_sites[i] = {
				name:$sites[i].name,
				id:$sites[i].id,
				visitors:'#visitors_'+$sites[i].id, 
				messages:'#messages', 
				url:$server.url, 
				interval:5000,
				load:function(){
					var $obj = this;
					$.ajax({
						async: false,
						url: $obj.url + 'lastActions&p[site_id]='+$obj.id+'&p[time]=' + $obj.time,
						dataType:"json",
						success: function(conts) {
							$($obj.visitors+' .offline').addClass('remove');
							$($obj.visitors+' .visitor').addClass('offline');
							if(conts.length > 0){
								for(j in conts){
									$obj.update(conts[j]);
								}
								$obj.time = conts[0].time;
								if(!$("#link_"+this.id).hasClass('a')){
									$("#link_"+this.id).addClass('alert');
								}
							}
							$($obj.visitors+' .remove').remove();
						}
					});
					$server.check(this.id);
					call = "$_sites["+this.index+"].load()";
					setTimeout(call, $obj.interval);
				},
				start:function(i){
					$("#tabs").append("<a href='javascript: void(openTab(\""+this.id+"\"));' id='link_"+this.id+"'>"+this.name+" <img src='images/alert.gif' alt='' class='vam' /></a>");
					$("#stats").append("<div id='visitors_"+this.id+"' class='stat'></div>");
					this.index = i;
					this.resetTime();
					this.load();
				},
				resetTime:function(){
					var $obj = this;
					$.ajax({
						async: false,
						url: $server.url + 'getTime',
						success: function(date) {
							$obj.time = date;
						}
					});		
				},				
				update:function(visit){
					visobj = $("#visit_"+this.id+"_"+visit.id);
					visobj.removeClass('offline');
					visobj.removeClass('remove');
					if(visobj.length > 0){
						visobj.find('.pages').html(visit.url + "(" + visit.page_count + ")" );
						visobj.find('.time').html(visit.visit_time); 
					}else{
						$(this.visitors).prepend("" +
							"<div class='visitor radius3' rel='"+visit.id+"' id='visit_"+this.id+"_"+visit.id+"'>" +
								"<img src='images/alert.gif' alt='' class='alert' />" +
								"<div class=\"from\">" + "<img src=\"images/countries/"+visit.country_code+".gif\" class=\"vam\" alt=\"\" /> " + visit.city + " (" + visit.ipaddress + ") - " + visit.browser + " " + visit.browser_version + "</div>" +
								"<div class=\"referer\"><em>" + visit.start_time + ":</em> " + visit.referer + "</div>" + 
								//"<div class=\"pages\"><em>" + visit.page_visit_time + ":</em> " + visit.url + "(" + visit.page_count + ")" + "</div>" + 
								"<div class=\"write\"><a href='javascript: void(chat(\""+this.id+"\", \""+visit.id+"\"))'>Write</a></div>" +
							"</div>");
						$("#messages").append("<form class='chat hide' id='chat_"+this.id+"_"+visit.id+"' action='javascript: void(sendMessage(\""+this.id+"\", \""+visit.id+"\"));'><div id='msgs_"+this.id+"_"+visit.id+"' class='msgs'></div><input type='text' class='txt vam radius3' name='txt' /> <input type='submit' value='send' class='btn vam radius3' /><div class='typeing'></div></form>");
					}
					visobj.addClass('new');					
				}
		}
		$_sites[i].start(i);
	}	
}


var $server = {
	url:"index.php?a=",
	loadSites:function(){
		$obj = this;
		$.ajax({
			async: false,
			url: $obj.url + 'loadSites',
			dataType:"json",
			success: function(conts) {
				createSites(conts);
			}
		});		
	},
	check:function(site_id){
		$obj = this;
		$.ajax({
			async: false,
			url: $obj.url + 'checkMessage&p[site_id]=' + site_id,
			dataType:"json",
			success: function(conts) {
				if(conts.length > 0){
					for(i in conts){
						$obj.add(conts[i]);
					}
				}
			}
		});		
	},
	send:function(site_id, visit_id, msg){
		$obj = this;
		if(!this.validateMsg(msg)){
			alert('Nothing to send.');
			return false;
		}
		$.ajax({
			async: false,
			url: $obj.url + 'sendMessage&p[site_id]=' + site_id + '&p[visit_id]=' + visit_id +  '&p[msg]=' + $obj.escape(msg),
			dataType:"json",
			success: function(conts) {
				$obj.add(conts);
				$obj.removeMessageAlert(site_id, visit_id);
			}
		});		
	},
	validateMsg:function(msg){
		return (msg!=''?true:false);
	},
	escape:function(msg){
		return msg;
	},	
	add:function(conts){
		msgs_obj = $("#msgs_"+conts.website_id+"_"+conts.visitor_id);
		msgs_obj.append("<div class='msg radius3 dir" + conts.direction + "'><em>" + conts.create_date + "</em><p>" + conts.message + "</p></div>");
		msgs_obj.animate({ scrollTop: msgs_obj.prop("scrollHeight") }, 3000);
		this.newMessageAlert(conts.website_id, conts.visitor_id);
	},
	newMessageAlert:function(site_id, visit_id){
		$("#visit_"+site_id+"_"+visit_id).addClass('newMessage');
		if(!$("#link_"+site_id).hasClass('a')){
			$("#link_"+site_id).addClass('alert');
		}
	},
	removeMessageAlert:function(site_id, visit_id){
		$("#visit_"+site_id+"_"+visit_id).removeClass('newMessage');
	}
}

var last_time = new Date();
last_time.setMinutes(last_time.getMinutes()-50);

var $_sites = new Array;


function chat(id, visit_id){
	$("#messages .chat").hide();
	chatobj = $("#chat_"+id+"_"+visit_id);
	chatobj.show();
}

function sendMessage(id, visit_id){
	txt = $("#chat_"+id+"_"+visit_id+" .txt");
	if(txt.val()==''){
		txt.addClass('err');
		return false;
	}
	$server.send(id, visit_id, txt.val());
	txt.val('');
}

function openTab(id){
	$("#tabs a").removeClass("a");
	$("#link_"+id).addClass("a");
	$("#link_"+id).removeClass('alert');

	$("#stats .stat").addClass("hide");
	$("#visitors_"+id).removeClass("hide");
}