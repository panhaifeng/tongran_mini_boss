<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title></title>
</head>

<body>
  <p>
    1,简单导出,(适用于单页数据,数据记录不多的情况,不发起服务器请求,直接从页面缓存中获得数据)<br />
    <button onclick="downloadExl(jsono)">导出</button>
  </p>
  <p>
    2,大数据导出,如果记录总数超过1000,发起多次ajax请求,每次请求1000条数据,待所有响应完毕后,用promise.all来进行统一导出 <br/>
     <a href='https://www.dragonballsoft.cn/archives/58.html'>PHP配合SheetJS/js-xlsx导出Excel大量数据</a>
  </p>
    <{webcontrol type='LoadJsCss' src="Resource/Script/xlsx.full.min.js"}>
    <!--调用FileSaver saveAs函数可以实现文件下载-->
    <!--<script src="http://sheetjs.com/demos/Blob.js"></script>
    <script src="http://sheetjs.com/demos/FileSaver.js"></script>-->
    <script>
        //如果使用 FileSaver.js 就不要同时使用以下函数
        function saveAs(obj, fileName) {//当然可以自定义简单的下载文件实现方式 
            var tmpa = document.createElement("a");
            tmpa.download = fileName || "下载";
            tmpa.href = URL.createObjectURL(obj); //绑定a标签
            tmpa.click(); //模拟点击实现下载
            setTimeout(function () { //延时释放
                URL.revokeObjectURL(obj); //用URL.revokeObjectURL()来释放这个object URL
            }, 100);
        }

        function s2ab(s) {
            if (typeof ArrayBuffer !== 'undefined') {
                var buf = new ArrayBuffer(s.length);
                var view = new Uint8Array(buf);
                for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            } else {
                var buf = new Array(s.length);
                for (var i = 0; i != s.length; ++i) buf[i] = s.charCodeAt(i) & 0xFF;
                return buf;
            }
        }
        var jsono = [{ //测试数据
            "保质期临期预警(天)": "adventLifecycle",
            "商品标题": "title",
            "建议零售价": "defaultPrice",
            "_1": "height",
            "_2": "Description",
            "保质期禁售(天)": "lockupLifecycle",
            "商品名称": "skuName",
            "商品简介": "brief",
            "宽(cm)": "width",
            "阿达": "asdz",
            "货号": "goodsNo",
            "商品条码": "skuNo",
            "商品品牌": "brand",
            "净容积(cm^3)": "netVolume",
            "是否保质期管理": "isShelfLifeMgmt",
            "是否串号管理": "isSNMgmt",
            "商品颜色": "color",
            "尺码": "size",
            "是否批次管理": "isBatchMgmt",
            "商品编号": "skuCode",
            "商品简称": "shortName",
            "毛重(g)": "grossWeight",
            "长(cm)": "length",
            "英文名称": "englishName",
            "净重(g)": "netWeight",
            "商品分类": "categoryId",
            "这里超过了": 1111.0,
            "保质期(天)": "expDate"
        }];        
        var opt = {header : ['尺码','商品编号']};
        const wopts = { bookType: 'xlsx', bookSST: false, type: 'binary' };//这里的数据是用来定义导出的格式类型
        // const wopts = { bookType: 'csv', bookSST: false, type: 'binary' };//csv格式
        // const wopts = { bookType: 'ods', bookSST: false, type: 'binary' };//ods格式
        // const wopts = { bookType: 'xlsb', bookSST: false, type: 'binary' };//xlsb格式
        // const wopts = { bookType: 'fods', bookSST: false, type: 'binary' };//fods格式
        // const wopts = { bookType: 'biff2', bookSST: false, type: 'binary' };//xls格式

        function downloadExl(data, type) {
            const wb = { SheetNames: ['Sheet1'], Sheets: {}, Props: {} };
            //如果opt中有header,优先显示header中的内容,
            //数据集中的其他字段也会补偿性显示.
            wb.Sheets['Sheet1'] = XLSX.utils.json_to_sheet(data,opt);//通过json_to_sheet转成单页(Sheet)数据
            saveAs(new Blob([s2ab(XLSX.write(wb, wopts))], { type: "application/octet-stream" }), "这里是下载的文件名" + '.' + (wopts.bookType=="biff2"?"xls":wopts.bookType));
        }
    </script>
</body>
</html>