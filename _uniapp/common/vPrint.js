const vioQsPrinter = uni.requireNativePlugin('violin-qsprinter');
// 封装加密方法
module.exports = {
    init() {
		vioQsPrinter.printOpen({"authcode":"5bi45bee5piT5aWH5L+h5oGv56eR5oqA5pyJ6ZmQ5YWs5Y+4IzIwMTkxMDE0MTEwMCN2aW9saW4jMTAyNTgyNTY0MQ=="},result => {
			/* const msg = JSON.stringify(result)
			console.log("init: "+msg) */
		})
    },
	close() {
		vioQsPrinter.printClose();
	},
	scan(){
		vioQsPrinter.scan({},result=>{
			const msg = JSON.stringify(result)
			console.log("scan:vprint "+msg)
		})
	},
	print(data){
		let printData ={
			"maxFontSize":50,
			"pageHeight":320,
			// "preview":1,
			"content":[
				{
					"type":"barcode",
					"text":'6934502301852',
					"x":10,"y":10,
					"width":300,"height":70,
					"format":"code_128"
				},
				{
					"type":"string",
					"text":"6934502301853",
					"x":70,"y":110,
					"align":"normal",
					"fontSize":26,"bold":"true"
				}
			]
		};
		vioQsPrinter.print(printData,result => {
			const msg = JSON.stringify(result)
			console.log("vPrint: "+msg)
		})
	},
}
