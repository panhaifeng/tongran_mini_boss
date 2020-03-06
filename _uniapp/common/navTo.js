// 封装加密方法
module.exports = {
    set(url,data) {
		let _url = ''
		for (var k in data) {
		  let value = data[k] !== undefined ? data[k] : ''
		  _url += '&' + k + '=' + encodeURIComponent(value)
		}
		let res = _url ? _url.substring(1) : '';
		url += (url.indexOf('?') < 0 ? '?' : '&') + res
		
		uni.navigateTo({  
			url
		}) 
    },
	//获取url
	formate(url,data){
		let _url = ''
		for (var k in data) {
		  let value = data[k] !== undefined ? data[k] : ''
		  _url += '&' + k + '=' + encodeURIComponent(value)
		}
		let res = _url ? _url.substring(1) : '';
		url += (url.indexOf('?') < 0 ? '?' : '&') + res
		
		return url;
	}
}
