function setCurrent() {
	var w = window.location.href;
	var n = document.getElementById("push");
	var l = n.getElementsByTagName("a");
	for(i=0;i<l.length;i++) {
		if(l[i].href == w) {
			l[i].className = "current";
			
		}
	}
}