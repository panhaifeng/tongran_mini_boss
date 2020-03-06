import md5 from 'js-md5'
// 封装加密方法
var formData = {
	urlStorageKey:'uni.server.url.tongran',
	httpUrl(){
		//uni.setStorageSync(this.urlStorageKey, "http://192.168.1.49/mini_demo/apiMini.php"); //调试情况
		//uni.setStorageSync('uni.userinfo.data.tongran.key', '{"userId":"22","userName":"hao","compName":"启瑞有限公司","openid":"oPbHavxu-ofXsbL6eFS59OtbRxaw","nickname":"大脑斧gg","realName":"王涛","timestamp":1576203643}');
		var url = uni.getStorageSync(this.urlStorageKey);
		if(url){
			return url;
		}else{
			uni.reLaunch({
				url: '/pages/login/bind'
			});
		}
	},
    set(params) {
		if(!params['method']) {
		    return false;
		}
		//时间戳
		let timestamp = Date.parse(new Date());
		timestamp = timestamp / 1000;
		//params默认值赋值
		params.timestamp = timestamp;
		params.version = 'uniapp';
		var token = 'Qpa72bVihMge9d0j1frG4fL4o7gbrc6N21ca4pQ35gbuzd4';
		
		//从缓存中获取openid
		var userinfo = this.getUserinfoStorage();
		// #ifdef MP-WEIXIN
		params.openid = userinfo.openid || '';
		params.nickname = userinfo.nickname || '';
		// #endif
		// #ifndef MP-WEIXIN
		params.sid = userinfo.sid || '';
		// #endif
		if(!params.userId)params.userId = userinfo.userId || '';
		if(!params.creater)params.creater = userinfo.realName || '';
		
		//sign处理
		let tmpSign = params.timestamp + '&' + params.method + '&' + params.version + '&' + token;
		if(params.sid){
			tmpSign = tmpSign + '&' + params.sid;
		}
		if(params.openid){
			tmpSign = tmpSign + '&' + params.openid;
		}
		if(params.userId){
			tmpSign = tmpSign + '&' + params.userId;
		}

		params.sign = md5(tmpSign);

		return params;
    },
	//封装搜索日期方法
	dateSearch(){
		var nowDate = new Date();
		        var lastMonthDay = new Date(nowDate.getTime() - 24*60*60*1000*30); //前一个月
		        var nowMonth = nowDate.getMonth()+1;
		        nowMonth = nowMonth<9?"0"+nowMonth:nowMonth;
		        var nowDay = nowDate.getDate()<10?"0"+nowDate.getDate():nowDate.getDate();
		        var yesterdayMonth = lastMonthDay.getMonth()+1;
		        yesterdayMonth = yesterdayMonth<9?"0"+yesterdayMonth:yesterdayMonth;
		        var yesterdayDay = lastMonthDay.getDate()<10?"0"+lastMonthDay.getDate():lastMonthDay.getDate();
		        var lastMonthDayStr = lastMonthDay.getFullYear() + "-" + yesterdayMonth + "-" + yesterdayDay;
			    var nowDateStr = nowDate.getFullYear() + "-" + nowMonth + "-" + nowDay;
				var dateInfo = {
					dateFrom:lastMonthDayStr,
					dateTo:nowDateStr,
				}
				
		return dateInfo;
	},
	dateToday(){
		var myDate = new Date();
		var thisyear = myDate.getFullYear();
		var thisM = myDate.getMonth()+1;
		var thisD = myDate.getDate();
		if(thisD<10) thisD = "0"+thisD;
		if(thisM<10) thisM = "0"+thisM;
		var date = thisyear+'-'+thisM+'-'+thisD;
		return date;
	},
	//封装可选可输入列表方法
	getAutoList(url,params){
		let retData = [];
		return uni.request({
				url: url,
				data:params
			}).then(ret => {
				var [error, res] = ret;
				let data1 = res.data.data.data || [];
				if (data1.length <= 0) {
					return retData = [];
				}
				for (let it of data1) {
					retData.push({
						text: it.name, //自定义数据对象必须要有text属性
						digest: it.id //其它字段根据业务需要添加
					});
				}
				return retData;
			});
	},
	verifyQrcodeToken(param){
		let timestamp = Date.parse(new Date());
		timestamp = timestamp / 1000;
				
		let timedifference = timestamp - (param.timestamp || 0);
		//60分钟有效期
		var expires_in_qr = 3600;
		if(param.test && param.test=='eqinfo'){
			//如果是测试帐号,有效期设置为5天
			expires_in_qr = 5*24*3600;
		}
		//判断二维码是否过期
		if(timedifference > expires_in_qr){
			return false;
		}
		
		var tokenStr = param.timestamp+'*'+param.serverUrl+'*'+param.uid+'*'+param.uname;
		var token = md5(tokenStr);
		if(token == param.token){
			return true;
		}
		
		return false;
	},
	storageUserKey:'uni.userinfo.data.tongran.key',
	expires_in:3600,//1h 3600
	getUserinfo(callback){ //获取用户的信息,没有则处理
		var user = this.getUserinfoStorage();
		if(user.userId){
			if(callback){
				callback(user);
				return true;
			}
		}
		
		// #ifdef H5
		var _resolve = null;
		// #endif
		
		//缓存中没有则需要刷新服务器
		// #ifdef MP-WEIXIN
		var _resolve = this.byweixin();
		// #endif
		
		// #ifdef APP-PLUS
		var _resolve = this.byapp();
		// #endif

		if(_resolve && _resolve.then){
			_resolve.then(callback || function(user){
				// console.log('触发默认callback事件',user);
				if(!user.userId){
					uni.showToast({icon:'none',title:"请先登录",duration:2500,mask:true});
					//如果没有有效的userid登录信息,则跳转提示需要登录
					setTimeout(function(){
						uni.reLaunch({
							url: '/pages/login/bind'
						});
					},1000)
				}
			});
			
			return true;
		}else{
			//如果什么信息都没有获取到,则callback一个空对象
			if(callback){
				callback({});
				return true;
			}else{
				//直接提示需要登录并跳转登录页面
				uni.showToast({icon:'none',title:"请先登录",duration:2500,mask:true});
				//如果没有有效的userid登录信息,则跳转提示需要登录
				setTimeout(function(){
					uni.reLaunch({
						url: '/pages/login/bind'
					});
				},1000);
				
				return false;
			}			
		}
	},
	byweixin(){		
		var _this = this;
		var doTask = new Promise(function (resolve, reject) {
			uni.login({
				provider: 'weixin',
				success: function (loginRes) {						
					uni.getUserInfo({
						provider: 'weixin',
						complete:function(infoRes){
							console.log('用户信息weixin：' , loginRes);
							//到服务器查获取用户对应的个人信息
							var params = {
								method:'login.user.mp',
								userinfo:JSON.stringify(loginRes),
								provider: 'weixin',
								nickname:infoRes.userInfo ? infoRes.userInfo.nickName : '',
							};
							// console.log('请求服务端：',params);
							formData.set(params);
							uni.request({
								url: formData.httpUrl(), //仅为示例，并非真实接口地址。
								data:params,
								method:'POST',
								header: {
								  'content-type': 'application/x-www-form-urlencoded'
								},
								success: (res) => {
									var result = res.data.data;
									console.log('result：',result);
									if(result.userinfo.openid != ''){
										result.userinfo.nickname = infoRes.userInfo ? infoRes.userInfo.nickName : '';
										_this.setUserinfoStorage(result.userinfo);
									}
									return resolve(result.userinfo);
								}
							});
						}
					});					
				}
			});			
		});
		
		return doTask;
	},
	byapp(){
		//待完成:获取信息在非微信小程序的平台,如app上
	},
	getUserinfoStorage(expires_in){
		if(!expires_in){
			expires_in = this.expires_in;
		}
		//所有平台用户信息缓存到本地都是一样的,区别在第一次获取和后期的服务器刷新状态
		let timestamp = Date.parse(new Date());
		timestamp = timestamp / 1000;
		
		var userinfo = uni.getStorageSync(this.storageUserKey);
		console.log('userinfo',userinfo);

		userinfo = userinfo ? JSON.parse(userinfo) : {};
		if(userinfo && userinfo.userName){
			//判断有效期是否正确
			let timedifference = timestamp - (userinfo.timestamp || 0);
			// console.log('timedifference',timedifference,'expires_in',expires_in);
			if (timedifference > expires_in) {
				//返回一个空的数据
			    userinfo = {};
			}
		}
		
		//刷新本地的身份信息
		// this.setUserinfoStorage(userinfo);
		
		//先从缓存中获取,如果本地有数据,并且未过期,则从本地获取
		return userinfo;
	},
	setUserinfoStorage(userinfo){		
		let timestamp = Date.parse(new Date());
		timestamp = timestamp / 1000;
		userinfo.timestamp = timestamp;
		
		uni.setStorageSync(this.storageUserKey,JSON.stringify(userinfo));
	},
	//处理当前切换的展会
	exhibitionCurKey : 'exhibition.current.key.eqinfo',
	getCurrentExhibition(){
		//所有平台用户信息缓存到本地都是一样的,区别在第一次获取和后期的服务器刷新状态
		let timestamp = Date.parse(new Date());
		timestamp = timestamp / 1000;
		var expires_in = 50*24*3600 ;//50天有效期
		
		var exhibition = uni.getStorageSync(this.exhibitionCurKey);
		exhibition = exhibition ? JSON.parse(exhibition) : {};
		
		if(exhibition && exhibition.id){
			//判断有效期是否正确
			let timedifference = timestamp - (exhibition.timestamp || 0);
			if (timedifference > expires_in) {
				//返回一个空的数据
			    exhibition = {};
			}
		}
		
		return exhibition;
	},
	setCurrentExhibition(exhibition){
		let timestamp = Date.parse(new Date());
		timestamp = timestamp / 1000;
		exhibition.timestamp = timestamp;
		
		uni.setStorageSync(this.exhibitionCurKey,JSON.stringify(exhibition));
	}
}
export default formData;