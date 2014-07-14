function loadRequests(pagenumber)
{
	url = 'requests/'+pagenumber;
	element = document.getElementById('request_change');
	req = new XMLHttpRequest();
	req.open("GET", url, false);
	req.send(null);
	element.innerHTML = req.responseText; 
}