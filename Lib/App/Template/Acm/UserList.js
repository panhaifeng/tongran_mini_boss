callbacks['QrCodeVerify'] = function(row,options) {
    var tipTitle = row.qrCodeVerify == 1 ? '确认取消验证吗' : '确认开启验证吗';
    this.$confirm(tipTitle, '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    }).then(() => {
      var url = "<{url controller=$smarty.get.controller action='QrCodeVerify'}>";
      var params = {id:row.id};
      this.$http.post(url,params).then((res)=>{
        if(!res.data.success) {
          this.$message(res.data.msg||'操作失败','error');
          // console.log(res);
          return false;
        }
        this.$message(res.data.msg||'操作成功','success');
        //刷新grid
        this._getRows();
        return;
      }).catch((error)=>{
        //服务器端返回success=false
        console.error(error);
      });
    }).catch(() => {
      //请求失败
      return false;
    });
}