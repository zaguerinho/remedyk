function getVideoId(text){
	if ((text.toLowerCase().indexOf('youtu.be/') !== -1) || (text.toLowerCase().indexOf('youtube.com/embed/') !== -1)){
		const id = text.split('/')[text.split('/').length - 1];
		return id;
	}
	else if (text.toLowerCase().indexOf('youtube.com/watch') !== -1) {
		const query = text.split('?')[1];
		const params = query.split('&');
		for (let i=0; i<params.length;i++){
			let param = params[i].split('=');
			if (param[0] == 'v'){
				const id = param[1];
				return id;
			}
		}
	}
	else if (text.indexOf('/') === -1 
		&& text.indexOf('?') === -1 
		&& text.indexOf(':') === -1 
		&& text.indexOf('&') === -1)
		return text;
	return '';
}

function getVideoEmbedUrl(text){
	const id = getVideoId(text);
	if (id == '')
		return '';
	return 'https://www.youtube.com/embed/'+id;
}

function getVideoUrl(text){
	const id = getVideoId(text);
	if (id == '')
		return '';
	return 'https://www.youtube.com/watch?v='+id;
}