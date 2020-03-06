// 通用脚本，个性化代码请不要写在这里面
callbacks['productId:select'] = function(row,e) {
    if(row._unit){
        this.$root.rowSon.cntKg = '';
        this.$root.sonFormItems.cntKg.disabled = true;
        this.$root.sonFormItems.cnt['append'] = '数量：'+row.unit;
    }else{
        this.$root.sonFormItems.cntKg.disabled = false;
        this.$root.sonFormItems.cnt['append'] = '平方米';
    }
}

callbacks['dialogSon:open'] = callbacks['dialogSon:handlePre'] = callbacks['dialogSon:handleNext'] = function(row){
    // console.log(row);
    if(row.productId){
        var url = "<{url controller='Jichu_Product' action='Unit'}>";
        var params = {productId:row.productId};
        this.$http.post(url,params).then((res)=>{
            res = res.data;
            // console.log(res);
            if(res._unit){
                this.$root.rowSon.cntKg = '';
                this.$root.sonFormItems.cnt['append'] = '数量：'+res.unit;
                this.$root.sonFormItems.cntKg.disabled = true;
            }else{
                this.$root.sonFormItems.cntKg.disabled = false;
                this.$root.sonFormItems.cnt['append'] = '平方米';
            }
        }).catch((error)=>{
            //服务器端返回success=false
            console.error(error);
        });
    }
}