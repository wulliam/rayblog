var marqueeInterval=new Array();
var marqueeId=0;
var marqueeDelay=4000;
var marqueeHeight=16;

function aryRandom (a) {
	var l=a.length;
	for(var i=0;i<l;i++) {
		var r=Math.floor(Math.random()*(l-i));
		a=a.slice(0,r).concat(a.slice(r+1)).concat(a[r]);
		}
	return a;
	}
function getObj(objId){
	if(navigator.appName!='Microsoft Internet Explorer'){
		return document.getElementById(objId);
	}else{
		try{
			return document.all[objId];
		}catch(e){return null;}
	}
	}
function initMarquee() {
	marqueeContent=aryRandom(marqueeContent);
	var str='';
	for(var i=0;i<Math.min(1,marqueeContent.length);i++) str+=(i>0?'　　':'')+marqueeContent[i];
	document.write('<div id="marqueeBox" style="overflow:hidden;height:'+marqueeHeight+'px" onmouseover="clearInterval(marqueeInterval[0])" onmouseout="marqueeInterval[0]=setInterval(\'startMarquee()\',marqueeDelay)"><div>'+str+'</div></div>');
	marqueeId++;
	if(marqueeContent.length>1)marqueeInterval[0]=setInterval("startMarquee()",marqueeDelay);
	}
function reinitMarquee() {
	getObj('js_scroll_content').src='scroll_content2.js';
	marqueeContent=marqueeContent.random();
	var str='';
	for(var i=0;i<Math.min(1,marqueeContent.length);i++) str+=(i>0?'　　':'')+marqueeContent[i];
	getObj('marqueeBox').childNodes[(getObj('marqueeBox').childNodes.length==1?0:1)].innerHTML=str;
	marqueeId=2;
	}
function startMarquee() {
	var str='';
	var marqueeBox=getObj('marqueeBox');
	for(var i=0;(i<1)&&(marqueeId+i<marqueeContent.length);i++) {
		str+=(i>0?'　　':'')+marqueeContent[marqueeId+i];
		}
	marqueeId++;
	if(marqueeId>marqueeContent.length)marqueeId=0;

	if(marqueeBox.childNodes.length==1) {
		var nextLine=document.createElement('DIV');
		nextLine.innerHTML=str;
		marqueeBox.appendChild(nextLine);
		}
	else {
		marqueeBox.childNodes[0].innerHTML=str;
		marqueeBox.appendChild(marqueeBox.childNodes[0]);
		marqueeBox.scrollTop=0;
		}
	clearInterval(marqueeInterval[1]);
	marqueeInterval[1]=setInterval("scrollMarquee()",20);
	}
function scrollMarquee() {
	getObj('marqueeBox').scrollTop++;
	if(getObj('marqueeBox').scrollTop%marqueeHeight==(marqueeHeight-1)){
		clearInterval(marqueeInterval[1]);
		}
	}
initMarquee();